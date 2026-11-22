<?php
require_once 'config.php';

if (!isset($_SESSION['student']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$student = $_SESSION['student'];
$student_id = $student['student_id'];
$type = $_POST['type'] ?? 'exam';
$submitted_answers = $_POST['answers'] ?? [];

// Fetch ALL questions assigned to the student's program and type
$stmt = $conn->prepare("
    SELECT q.id, q.correct_option 
    FROM questions q 
    JOIN subjects s ON q.subject_id = s.id 
    JOIN program_subjects ps ON s.id = ps.subject_id 
    JOIN programs p ON ps.program_id = p.id 
    WHERE p.program_key = ? AND q.type = ?
");
$stmt->bind_param("ss", $student['program_key'], $type);
$stmt->execute();
$all_questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total_score = 0;
$max_score = count($all_questions);

if ($max_score > 0) {
    // Record initial submission entry
    $stmt = $conn->prepare("INSERT INTO submissions (student_id, type, total_score, max_score, percentage) VALUES (?, ?, 0, ?, 0)");
    $stmt->bind_param("ssi", $student_id, $type, $max_score);
    $stmt->execute();
    $submission_id = $stmt->insert_id;

    $answer_stmt = $conn->prepare("INSERT INTO submission_answers (submission_id, question_id, user_answer, is_correct) VALUES (?, ?, ?, ?)");

    foreach ($all_questions as $q) {
        $q_id = $q['id'];
        $correct_option = $q['correct_option'];

        // If the student did not answer this question, mark it as '-' (unanswered/failed)
        if (isset($submitted_answers[$q_id]) && !empty($submitted_answers[$q_id])) {
            $user_ans = $submitted_answers[$q_id];
        } else {
            $user_ans = '-';
        }

        $is_correct = ($user_ans === $correct_option) ? 1 : 0;
        if ($is_correct) {
            $total_score++;
        }

        $answer_stmt->bind_param("iisi", $submission_id, $q_id, $user_ans, $is_correct);
        $answer_stmt->execute();
    }

    // Calculate percentage and update the main submission record
    $percentage = ($total_score / $max_score) * 100;
    $conn->query("UPDATE submissions SET total_score = $total_score, percentage = $percentage WHERE id = $submission_id");

    $_SESSION['last_submission_id'] = $submission_id;
}

header("Location: results.php");
exit;