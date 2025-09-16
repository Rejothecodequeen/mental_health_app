@php
    $now = now()->format('Y-m-d\TH:i'); // current date & time
@endphp

<div class="mb-3">
    <label for="counselor_id" class="form-label"><i class="bi bi-person-badge"></i> Counselor</label>
    @if($counselors->isEmpty())
        <div class="alert alert-warning">
            No counselors available. Please contact admin.
        </div>
    @else
        <select name="counselor_id" id="counselor_id" class="form-select @error('counselor_id') is-invalid @enderror" required>
            <option value="">-- Select a Counselor --</option>
            @foreach($counselors as $c)
                <option value="{{ $c->id }}" @selected(old('counselor_id', $appointment->counselor_id ?? '') == $c->id)>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
        @error('counselor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    @endif
</div>

<div class="mb-3">
    <label for="start_time" class="form-label"><i class="bi bi-calendar-plus"></i> Start Time</label>
    <input type="datetime-local" name="start_time" id="start_time"
           value="{{ old('start_time', isset($appointment) ? $appointment->start_time->format('Y-m-d\TH:i') : $now) }}"
           min="{{ $now }}"
           class="form-control @error('start_time') is-invalid @enderror" required>
    <div class="form-text">Choose the start time for your appointment (today or later).</div>
    @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="end_time" class="form-label"><i class="bi bi-calendar-check"></i> End Time</label>
    <input type="datetime-local" name="end_time" id="end_time"
           value="{{ old('end_time', isset($appointment) ? $appointment->end_time->format('Y-m-d\TH:i') : $now) }}"
           min="{{ $now }}"
           class="form-control @error('end_time') is-invalid @enderror" required>
    <div class="form-text">Choose the end time for your appointment (today or later).</div>
    @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="notes" class="form-label"><i class="bi bi-pencil-square"></i> Notes</label>
    <textarea name="notes" id="notes" rows="3"
              class="form-control @error('notes') is-invalid @enderror"
              placeholder="Optional notes for the counselor">{{ old('notes', $appointment->notes ?? '') }}</textarea>
    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

@if(isset($appointment))
<div class="mb-3">
    <label for="status" class="form-label"><i class="bi bi-info-circle"></i> Status</label>
    <select name="status" id="status" class="form-select">
        @foreach(['booked','cancelled','completed'] as $s)
            <option value="{{ $s }}" @selected(old('status', $appointment->status) == $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
</div>
@endif
