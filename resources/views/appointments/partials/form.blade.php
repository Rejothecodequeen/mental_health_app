@csrf
<div class="mb-3">
    <label for="counselor_id" class="form-label">Counselor</label>
    <select name="counselor_id" id="counselor_id" class="form-select" required>
        @foreach(App\Models\User::where('role','counselor')->get() as $c)
            <option value="{{ $c->id }}" @selected(old('counselor_id',$appointment->counselor_id ?? '')==$c->id)>{{ $c->name }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Start Time</label>
    <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time', isset($appointment)?$appointment->start_time->format('Y-m-d\TH:i'):'') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">End Time</label>
    <input type="datetime-local" name="end_time" class="form-control" value="{{ old('end_time', isset($appointment)?$appointment->end_time->format('Y-m-d\TH:i'):'') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $appointment->notes ?? '') }}</textarea>
</div>
@if(isset($appointment))
<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select">
        @foreach(['booked','cancelled','completed'] as $s)
            <option value="{{ $s }}" @selected($appointment->status==$s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
</div>
@endif
<button class="btn btn-primary">Save</button>
