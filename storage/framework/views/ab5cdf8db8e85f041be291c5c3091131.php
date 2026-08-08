<form action="<?php echo e(route('invoices.destroy', $getRecord()->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete?');">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button type="submit" style="color: red; cursor: pointer; border: none; background: none;">
        🗑️ Delete
    </button>
</form>
