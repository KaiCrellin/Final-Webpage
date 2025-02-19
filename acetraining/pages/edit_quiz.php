<div class="dashboard-card">
    <h2 class="card-title">Quiz Information</h2>
    <div class="detail-row">
        <strong>Created:</strong>
        <?php echo date('M d, Y H:i', strtotime($quiz['created_at'])); ?>
    </div>
    <div class="detail-row">
        <strong>Students in Course:</strong>
        <?php echo $quiz['enrolled_students']; ?>
    </div>
    <div class="form-actions">
        <form method="POST" action="/acetraining/handlers/delete_quiz.php" style="display: inline;"
            onsubmit="return confirm('Are you sure you want to delete this quiz? This cannot be undone.');">
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="dashboard-button danger">Delete Quiz</button>
        </form>
    </div>
</div>

</div>
</div>
// ...existing code...