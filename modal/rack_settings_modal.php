<!-- Rack Settings Modal -->
<div class="modal fade" id="rackSettingsModal" tabindex="-1" aria-labelledby="rackSettingsLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="settingsForm" class="modal-form">
        <div class="modal-header">
          <h5 class="modal-title" id="rackSettingsLabel">Rack Settings</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-12">
            <label for="itemWeight" class="form-label mb-0">Item Weight (kg)</label>
            <input type="number" step="0.01" min="0.01" id="itemWeight" name="itemWeight" class="form-control" required>
            <small class="text-muted">Set the weight of a single item in kilograms (base unit)</small>
          </div>
          <div class="col-12">
            <label for="tolerance" class="form-label mb-0">Tolerance (%)</label>
            <input type="number" step="0.1" min="0.1" max="10" id="tolerance" name="tolerance" class="form-control" required>
            <small class="text-muted">Set tolerance as percentage (recommended: 2%). Valid values: 0.1% - 10%</small>
          </div>
          <div class="col-12 mt-3">
            <div class="alert alert-info">
              <small><i class="fas fa-info-circle"></i> <strong>Note:</strong> Item weight is always stored in kg (base unit). The metric system dropdown only affects how values are displayed.</small>
            </div>
          </div>
          <div id="settingsMsg" class="mt-2"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>