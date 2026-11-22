<?php
require_once 'config.php';

if (!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit;
}

$type = isset($_GET['type']) &&$_GET['type'] === 'test' ? 'test' : 'exam';
$student =$_SESSION['student'];

// Fetch enrolled subjects for student's program
$stmt =$conn->prepare("
    SELECT s.id, s.subject_name 
    FROM subjects s 
    JOIN program_subjects ps ON s.id = ps.subject_id 
    JOIN programs p ON ps.program_id = p.id 
    WHERE p.program_key = ?
");
$stmt->bind_param("s", $student['program_key']);$stmt->execute();
$subjects =$stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch all questions for these subjects
$questions = [];
foreach ($subjects as$sub) {
    $q_stmt =$conn->prepare("SELECT * FROM questions WHERE subject_id = ? AND type = ?");
    $q_stmt->bind_param("is", $sub['id'],$type);
    $q_stmt->execute();$questions[$sub['id']] =$q_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jerro Computer School - Assessment Portal</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align: center; background: #1e293b; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #334155; }
        .timer { font-size: 20px; font-weight: bold; color: #f59e0b; background: #0f172a; padding: 8px 16px; border-radius: 6px; border: 1px solid #475569; }
        
        /* Subject Navigation Tabs */
        .subject-tabs { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .tab-btn { padding: 12px 20px; background: #1e293b; border: 1px solid #334155; color: #94a3b8; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 15px; transition: all 0.2s; }
        .tab-btn:hover { background: #334155; color: #fff; }
        .tab-btn.active { background: #0284c7; color: #fff; border-color: #38bdf8; }

        .q-card { background: #1e293b; padding: 20px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #334155; }
        .options { margin-top: 12px; }
        .options label { display: block; margin-bottom: 8px; background: #0f172a; padding: 12px; border-radius: 6px; cursor: pointer; border: 1px solid #334155; transition: 0.2s; }
        .options label:hover { background: #1e293b; border-color: #0284c7; }
        .submit-btn { padding: 14px 28px; background: #16a34a; color: white; font-size: 16px; font-weight: bold; border: none; border-radius: 6px; cursor: pointer; float: right; margin-top: 20px; }
        .submit-btn:hover { background: #15803d; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2 style="margin: 0;">Jerro Computer School Limited</h2>
            <small style="color: #94a3b8;">Welcome, <?php echo htmlspecialchars($student['full_name']); ?> Assessment: <?php echo strtoupper($type); ?></small>
        </div>
        <div class="timer" id="timer">30:00</div>
    </div>

    <!-- Dynamic Subject Navigation Tabs -->
    <div class="subject-tabs">
        <?php foreach ($subjects as $index =>$sub): ?>
            <button type="button" class="tab-btn <?php echo $index === 0 ? 'active' : ''; ?>" onclick="switchSubject(<?php echo $sub['id']; ?>, this)">
                <?php echo htmlspecialchars($sub['subject_name']); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <form id="examForm" action="submit_exam.php" method="POST">
        <input type="hidden" name="type" value="<?php echo $type; ?>">
        
        <?php foreach ($subjects as $s_index =>$sub): ?>
            <div class="subject-block" id="sub-block-<?php echo $sub['id']; ?>" style="<?php echo $s_index > 0 ? 'display:none;' : ''; ?>">
                <h3 style="color: #38bdf8; border-bottom: 2px solid #334155; padding-bottom: 8px;"><?php echo htmlspecialchars($sub['subject_name']); ?> Section</h3>
                
                <?php if (empty($questions[$sub['id']])): ?>
                    <p style="color: #94a3b8;">No questions currently configured for this subject.</p>
                <?php else: ?>
                    <?php foreach ($questions[$sub['id']] as $q_index =>$q): ?>
                        <div class="q-card">
                            <p><strong>Question <?php echo ($q_index + 1); ?>:</strong> <?php echo htmlspecialchars($q['question_text']); ?></p>
                            <div class="options">
                                <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="A"> A) <?php echo htmlspecialchars($q['option_a']); ?></label>
                                <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="B"> B) <?php echo htmlspecialchars($q['option_b']); ?></label>
                                <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="C"> C) <?php echo htmlspecialchars($q['option_c']); ?></label>
                                <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="D"> D) <?php echo htmlspecialchars($q['option_d']); ?></label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="submit-btn" onclick="return confirm('Are you sure you want to finish and submit your exam?')">Submit Final Assessment</button>
    </form>

    <script>
        // Switch between subject tabs seamlessly
        function switchSubject(subjectId, btnElement) {
            const blocks = document.querySelectorAll('.subject-block');
            blocks.forEach(block => block.style.display = 'none');

            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => btn.classList.remove('active'));

            document.getElementById('sub-block-' + subjectId).style.display = 'block';
            btnElement.classList.add('active');
        }

        // Exam Timer Countdown
        let seconds = 1800; // 30 Minutes
        const timerEl = document.getElementById('timer');
        
        const countdown = setInterval(() => {
            seconds--;
            let mins = Math.floor(seconds / 60);
            let secs = seconds % 60;
            timerEl.textContent = `${mins}:${secs < 10 ? '0' : ''}${secs}`;
            if (seconds <= 0) {
                clearInterval(countdown);
                alert("Time has expired! Submitting your answers automatically.");
                document.getElementById('examForm').submit();
            }
        }, 1000);
    </script>
</body>
</html>
