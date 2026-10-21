<?php
require_once 'models/AcademicSession.php';

class AcademicSessionController extends Controller {
    public function index($slug) {
        $school_id = current_school_id();
        $sessions = AcademicSession::getAllForSchool($school_id);
        
        $this->view('academic_sessions/index', [
            'sessions' => $sessions
        ]);
    }

    public function create($slug) {
        $this->view('academic_sessions/create');
    }

    public function store($slug) {
        $school_id = current_school_id();
        
        $name = $_POST['name'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $is_current = isset($_POST['is_current']) ? 1 : 0;
        
        if(empty($name) || empty($start_date) || empty($end_date)) {
            Session::set('error', 'All fields are required.');
            header("Location: /" . $slug . "/academic-sessions/create");
            exit;
        }

        AcademicSession::create([
            'school_id' => $school_id,
            'name' => $name,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'is_current' => $is_current
        ]);

        Session::set('success', 'Academic Session created successfully.');
        header("Location: /" . $slug . "/academic-sessions");
        exit;
    }

    public function edit($slug, $id) {
        $school_id = current_school_id();
        $session = AcademicSession::getById($id, $school_id);
        
        if(!$session) {
            Session::set('error', 'Academic Session not found.');
            header("Location: /" . $slug . "/academic-sessions");
            exit;
        }

        $this->view('academic_sessions/edit', [
            'session' => $session
        ]);
    }

    public function update($slug, $id) {
        $school_id = current_school_id();
        
        $name = $_POST['name'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $is_current = isset($_POST['is_current']) ? 1 : 0;
        
        if(empty($name) || empty($start_date) || empty($end_date)) {
            Session::set('error', 'All fields are required.');
            header("Location: /" . $slug . "/academic-sessions/" . $id . "/edit");
            exit;
        }

        AcademicSession::update($id, $school_id, [
            'name' => $name,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'is_current' => $is_current
        ]);

        Session::set('success', 'Academic Session updated successfully.');
        header("Location: /" . $slug . "/academic-sessions");
        exit;
    }
}
