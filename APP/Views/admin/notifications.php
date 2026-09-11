{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="card card-custom wow animate__animated animate__fadeInUp">
    <div class="card-header card-custom-header">
        <h5 class="mb-0"><i class="fa-solid fa-envelope me-2"></i> SMTP Settings & Dynamic Triggers</h5>
    </div>
    
    <div class="card-body card-custom-body">
        <form action="{{ pathto('admin/notifications') }}" method="POST">
            <div class="row bg-light p-4 rounded border mb-4">
                <h5 class="mb-3 border-bottom pb-2 text-primary font-weight-bold"><i class="fa-solid fa-server me-2"></i> Mail server parameters</h5>
                <div class="col-md-6 mb-3">
                    <label for="smtp_host" class="form-label font-weight-bold">SMTP Host</label>
                    <input type="text" name="smtp_host" id="smtp_host" class="form-control" value="{{ htmlspecialchars($smtp_host ?? '', ENT_QUOTES, 'UTF-8') }}" placeholder="mail.yourdomain.com">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="smtp_user" class="form-label font-weight-bold">SMTP Username</label>
                    <input type="text" name="smtp_user" id="smtp_user" class="form-control" value="{{ htmlspecialchars($smtp_user ?? '', ENT_QUOTES, 'UTF-8') }}" placeholder="smtp@yourdomain.com">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="smtp_pass" class="form-label font-weight-bold">SMTP Password</label>
                    <input type="password" name="smtp_pass" id="smtp_pass" class="form-control" value="{{ htmlspecialchars($smtp_pass ?? '', ENT_QUOTES, 'UTF-8') }}" placeholder="SMTP password">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="smtp_port" class="form-label font-weight-bold">SMTP Port</label>
                    <input type="number" name="smtp_port" id="smtp_port" class="form-control" value="{{ htmlspecialchars($smtp_port ?? '587', ENT_QUOTES, 'UTF-8') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="smtp_encryption" class="form-label font-weight-bold">Encryption</label>
                    <select name="smtp_encryption" id="smtp_encryption" class="form-select">
                        <option value="tls" <?= ($smtp_encryption ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Recommended)</option>
                        <option value="ssl" <?= ($smtp_encryption ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        <option value="none" <?= ($smtp_encryption ?? '') === 'none' ? 'selected' : '' ?>>None</option>
                    </select>
                </div>
            </div>

            <div class="row bg-light p-4 rounded border mb-4">
                <h5 class="mb-3 border-bottom pb-2 text-primary font-weight-bold"><i class="fa-solid fa-bell me-2"></i> Event-Driven Notification Triggers</h5>
                <p class="text-muted small">Select which operations should automatically fire email alerts to site administrator email.</p>
                
                <?php 
                $triggers = $notify_triggers ?? [];
                $availableTriggers = [
                    'user_registered' => 'New User Registration',
                    'post_published' => 'New Content Published',
                    'comment_added' => 'New User Comment (Requires approval)',
                    'option_modified' => 'Site Config Modifed',
                    'module_installed' => 'New Extension/Theme Installed'
                ];
                ?>

                <?php foreach ($availableTriggers as $key => $label) { ?>
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch p-3 border bg-white rounded shadow-sm d-flex align-items-center justify-content-between">
                            <label class="form-check-label font-weight-bold mb-0 ps-3" for="trigger_{{ $key }}">{{ $label }}</label>
                            <input class="form-check-input" type="checkbox" name="notify_triggers[]" value="{{ $key }}" id="trigger_{{ $key }}" <?= in_array($key, $triggers, true) ? 'checked' : '' ?> style="transform: scale(1.3); margin-right: 10px;">
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-5 py-2"><i class="fa-solid fa-save me-2"></i> Save Notification Settings</button>
            </div>
        </form>
    </div>
</div>

{{ $this->view('admin/layout/footer') }}
