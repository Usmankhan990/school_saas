<?php
/**
 * OnlineClassController – Live class management
 */
class OnlineClassController extends Controller
{
    public function index(): void
    {
        $schoolId = current_school_id();
        $classes = OnlineClass::allForSchool($schoolId);
        $this->view('online-classes.index', [
            'title'   => 'Online Classes',
            'classes' => $classes,
        ]);
    }

    public function create(): void
    {
        $schoolId = current_school_id();
        $classList  = ClassModel::allForSchool($schoolId);
        $sections   = SectionModel::allForSchool($schoolId);
        $teachers   = Teacher::allForSchool($schoolId);
        $this->view('online-classes.create', [
            'title'     => 'Schedule Online Class',
            'classList' => $classList,
            'sections'  => $sections,
            'teachers'  => $teachers,
        ]);
    }

    public function store(): void
    {
        $schoolId = current_school_id();
        $title       = $this->input('title');
        $classId     = (int) $this->input('class_id');
        $sectionId   = (int) $this->input('section_id') ?: null;
        $teacherId   = (int) $this->input('teacher_id');
        $scheduledAt = $this->input('scheduled_at');
        $duration    = (int) $this->input('duration_minutes') ?: 60;
        $meetingLink = $this->input('meeting_link');
        $platform    = $this->input('platform') ?: 'zoom';
        $description = $this->input('description');

        if (!$title || !$meetingLink || !$scheduledAt) {
            flash('error', 'Title, meeting link, and schedule time are required.');
            $this->saveInput();
            redirect(tenant_slug() . '/online-classes/create');
        }

        $id = OnlineClass::create([
            'school_id'       => $schoolId,
            'teacher_id'      => $teacherId,
            'class_id'        => $classId,
            'section_id'      => $sectionId,
            'title'           => $title,
            'description'     => $description,
            'scheduled_at'    => $scheduledAt,
            'duration_minutes'=> $duration,
            'meeting_link'    => $meetingLink,
            'platform'        => $platform,
        ]);

        // Notify students
        try {
            Notification::notifyClass($schoolId, $classId, 'New Online Class: ' . $title,
                "Class scheduled for " . date('d M Y h:i A', strtotime($scheduledAt)), 'online_class');
        } catch (Exception $e) {}

        $this->audit('create', 'online_class', $id);
        flash('success', 'Online class scheduled successfully.');
        redirect(tenant_slug() . '/online-classes');
    }

    public function join($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        $class = OnlineClass::findSecure($id, $schoolId);
        if (!$class) {
            flash('error', 'Class not found.');
            redirect(tenant_slug() . '/dashboard');
            return;
        }

        // Find the student record for current user
        $userId = current_user_id();
        $student = Database::fetch(
            "SELECT * FROM students WHERE user_id = ? AND school_id = ?",
            [$userId, $schoolId]
        );

        if ($student) {
            // Add participant log
            OnlineClass::addParticipant($id, $student['id']);

            // Auto mark attendance
            Attendance::autoMarkFromOnlineClass(
                $schoolId,
                $class['class_id'],
                $class['section_id'],
                $student['id'],
                $class['teacher_id']
            );
        }

        $this->audit('join', 'online_class', $id);

        // Redirect to meeting link
        header('Location: ' . $class['meeting_link']);
        exit;
    }

    public function updateStatus($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        $status = $this->input('status');

        OnlineClass::update($id, ['status' => $status]);
        $this->audit('update_status', 'online_class', $id);
        flash('success', 'Class status updated.');
        redirect(tenant_slug() . '/online-classes');
    }

    public function participants($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        $class = OnlineClass::findSecure($id, $schoolId);
        if (!$class) {
            flash('error', 'Class not found.');
            redirect(tenant_slug() . '/online-classes');
        }

        $participants = OnlineClass::getParticipants($id);
        $this->view('online-classes.participants', [
            'title'        => 'Participants - ' . $class['title'],
            'class'        => $class,
            'participants' => $participants,
        ]);
    }
}
