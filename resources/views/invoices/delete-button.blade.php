<form action="{{ route('invoices.destroy', $getRecord()->id) }}" method="POST"
    onsubmit="return confirm('Are you sure you want to delete?');">
    @csrf
    @method('DELETE')
    <button type="submit" style="color: red; cursor: pointer; border: none; background: none;">
        🗑️ Delete
    </button>
</form>
