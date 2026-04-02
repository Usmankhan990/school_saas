<?php
/**
 * SettingsController – School settings (logo, profile, academic session)
 */
class SettingsController extends Controller
{
    public function index(): void
    {
        $this->authorize(['super_admin', 'admin']);
        $school = School::find(current_school_id());

        $this->view('settings.index', [
            'title'  => 'School Settings',
            'school' => $school,
        ]);
    }

    public function update(): void
    {
        $this->authorize(['super_admin', 'admin']);
        $schoolId = current_school_id();
        $school = School::find($schoolId);

        // Handle logo upload
        $logoUrl = $school['logo_url'];
        $uploaded = $this->uploadFile('logo', "schools/{$schoolId}");
        if ($uploaded) {
            $logoUrl = $uploaded;
        }

        School::update($schoolId, [
            'name'             => $this->input('name') ?: $school['name'],
            'address'          => $this->input('address'),
            'phone'            => $this->input('phone'),
            'principal_name'   => $this->input('principal_name'),
            'academic_session' => $this->input('academic_session'),
            'currency'         => $this->input('currency') ?: DEFAULT_CURRENCY,
            'logo_url'         => $logoUrl,
        ]);

        // Update session
        Session::set('tenant_name', $this->input('name') ?: $school['name']);
        Session::set('tenant_logo_url', $logoUrl);

        $this->audit('update', 'school_settings', $schoolId);
        flash('success', 'Settings updated successfully!');
        redirect(tenant_slug() . '/settings');
    }
}
