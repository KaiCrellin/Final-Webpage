<div class="dashboard-card">
    <h2 class="card-title">Assignment Information</h2>
    <div class="detail-row">
        <strong>Created:</strong>
        <?php echo date('M d, Y H:i', strtotime($assignment['created_at'])); ?>
    </div>
    <div class="detail-row">
        <strong>Students in Course:</strong>
        <?php echo $assignment['enrolled_students']; ?>
    </div>
    <div class="form-actions">
        <form method="POST" action="/acetraining/handlers/delete_assignment.php" style="display: inline;"
            onsubmit="return confirm('Are you sure you want to delete this assignment? This cannot be undone.');">
            <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="dashboard-button danger">Delete Assignment</button>
        </form>
    </div>
</div>

</div>
</div>