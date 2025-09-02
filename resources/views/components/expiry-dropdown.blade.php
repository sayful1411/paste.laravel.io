<div>
    <div x-data="{ expiry: 'never' }">
        <label for="expiry" class="block text-sm font-medium text-gray-700 mb-1">Expires In</label>
        <select name="expiry" id="expiry" class="form-select w-full px-4 py-2 border" x-model="expiry">
            <option value="1_hour">1 hour</option>
            <option value="1_day">1 day</option>
            <option value="1_week">1 week</option>
            <option value="custom">Custom</option>
            <option value="never">Never</option>
        </select>
        <div class="mt-2" x-show="expiry === 'custom'">
            <label for="custom_expiry" class="block text-sm font-medium text-gray-700 mb-1">Select Date & Time</label>
            <input type="datetime-local" name="custom_expiry" id="custom_expiry" class="form-input w-full px-4 py-2 border" />
        </div>
    </div>
</div>
