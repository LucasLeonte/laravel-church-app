<div>
    <label>Title</label>
    <input type="text" name="title" value="{{ old('title', $program->title ?? '') }}" required>
</div>
<div>
    <label>Day of week</label>
    <input type="text" name="day_of_week" value="{{ old('day_of_week', $program->day_of_week ?? '') }}" placeholder="Sunday, Monday...">
</div>
<div>
    <label>Start time</label>
    <input type="time" name="start_time" value="{{ old('start_time', isset($program->start_time) ? substr($program->start_time,0,5) : '') }}">
</div>
<div>
    <label>End time</label>
    <input type="time" name="end_time" value="{{ old('end_time', isset($program->end_time) ? substr($program->end_time,0,5) : '') }}">
</div>
<div>
    <label>Description</label>
    <textarea name="description">{{ old('description', $program->description ?? '') }}</textarea>
</div>
<div>
    <label>Published</label>
    <!-- hidden field ensures a value is always submitted when checkbox is unchecked -->
    <input type="hidden" name="published" value="0">
    <input type="checkbox" name="published" value="1" {{ (bool) old('published', $program->published ?? false) ? 'checked' : '' }}>
</div>
