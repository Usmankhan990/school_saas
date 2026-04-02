<?php
/**
 * FeeController – Fee types, structures, assignment, and student fees
 */
class FeeController extends Controller
{
    // ── Fee Types ──

    public function types(): void
    {
        $schoolId = current_school_id();
        $types = FeeType::allForSchool($schoolId);
        $this->view('fees.types', ['title' => 'Fee Types', 'types' => $types]);
    }

    public function createType(): void
    {
        $this->view('fees.create-type', ['title' => 'Add Fee Type']);
    }

    public function storeType(): void
    {
        $schoolId = current_school_id();
        $name = $this->input('name');
        $desc = $this->input('description');
        if (!$name) { flash('error', 'Name is required.'); $this->saveInput(); redirect(tenant_slug() . '/fees/types/create'); }
        FeeType::create(['school_id' => $schoolId, 'name' => $name, 'description' => $desc]);
        $this->audit('create', 'fee_type');
        flash('success', 'Fee type created.');
        redirect(tenant_slug() . '/fees/types');
    }

    public function editType($id): void
    {
        $id = (int) $id;
        $type = FeeType::findSecure($id, current_school_id());
        if (!$type) { flash('error', 'Not found.'); redirect(tenant_slug() . '/fees/types'); }
        $this->view('fees.edit-type', ['title' => 'Edit Fee Type', 'type' => $type]);
    }

    public function updateType($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        FeeType::update($id, ['name' => $this->input('name'), 'description' => $this->input('description')]);
        flash('success', 'Fee type updated.');
        redirect(tenant_slug() . '/fees/types');
    }

    public function deleteType($id): void
    {
        $id = (int) $id;
        FeeType::delete($id, current_school_id());
        flash('success', 'Fee type deleted.');
        redirect(tenant_slug() . '/fees/types');
    }

    // ── Fee Structures ──

    public function structures(): void
    {
        $schoolId = current_school_id();
        $structures = FeeStructure::allForSchool($schoolId);
        $this->view('fees.structures', ['title' => 'Fee Structures', 'structures' => $structures]);
    }

    public function createStructure(): void
    {
        $schoolId = current_school_id();
        $types   = FeeType::allForSchool($schoolId);
        $classes = ClassModel::allForSchool($schoolId);
        $this->view('fees.create-structure', ['title' => 'Create Fee Structure', 'types' => $types, 'classes' => $classes]);
    }

    public function storeStructure(): void
    {
        $schoolId = current_school_id();
        $data = [
            'school_id'     => $schoolId,
            'fee_type_id'   => (int) $this->input('fee_type_id'),
            'class_id'      => (int) $this->input('class_id') ?: null,
            'amount'        => (float) $this->input('amount'),
            'due_date'      => $this->input('due_date') ?: null,
            'academic_year' => $this->input('academic_year'),
        ];
        if (!$data['fee_type_id'] || !$data['amount']) {
            flash('error', 'Fee type and amount are required.');
            $this->saveInput();
            redirect(tenant_slug() . '/fees/structures/create');
        }
        FeeStructure::create($data);
        $this->audit('create', 'fee_structure');
        flash('success', 'Fee structure created.');
        redirect(tenant_slug() . '/fees/structures');
    }

    public function deleteStructure($id): void
    {
        $id = (int) $id;
        FeeStructure::delete($id, current_school_id());
        flash('success', 'Fee structure deleted.');
        redirect(tenant_slug() . '/fees/structures');
    }

    /**
     * Assign a fee structure to all students in the class.
     */
    public function assignStructure($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        $count = FeeStructure::assignToClass($id, $schoolId);
        flash('success', "Fee assigned to {$count} students.");
        redirect(tenant_slug() . '/fees/structures');
    }

    // ── Student Fees ──

    public function studentFees(): void
    {
        $schoolId = current_school_id();
        $status = $_GET['status'] ?? '';
        $fees = StudentFee::allForSchool($schoolId, $status);
        $this->view('fees.student-fees', ['title' => 'Student Fees', 'fees' => $fees, 'currentStatus' => $status]);
    }

    public function defaulters(): void
    {
        $schoolId = current_school_id();
        $classId = (int)($_GET['class_id'] ?? 0);
        $classes = ClassModel::allForSchool($schoolId);
        $defaulters = StudentFee::getDefaulters($schoolId, $classId);
        $this->view('fees.defaulters', [
            'title'      => 'Defaulters',
            'defaulters' => $defaulters,
            'classes'    => $classes,
            'classId'    => $classId,
        ]);
    }
}
