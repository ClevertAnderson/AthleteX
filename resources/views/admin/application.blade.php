@extends('layouts.app') 

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0">
        
        <div class="col-md-3 col-lg-2 p-3" style="background-color: #dbead5; min-height: 100vh; border-right: 1px solid #c4d79b;">
            @include('admin.partials.sidebar')
        </div>

        <div class="col-md-9 col-lg-10 p-4" style="background-color: #EBF1DE;">
            
            <div class="card-header fw-bold fst-italic border-bottom border-secondary mb-4 p-2" style="background-color: #bfbfbf; font-family: 'Courier New';">
                ADMIN > APPLICATION SETTINGS
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body" style="background-color: #EBF1DE;">
                    
                    <form action="{{ route('admin.saveSettings') }}" method="POST">
                        @csrf
                        <div class="section-box border p-3 mb-3" style="background-color: #DCE6F1; border: 1px solid #999;">
                            <h6 class="fw-bold fst-italic border-bottom border-secondary pb-1 mb-3">APPLICATION SETTINGS</h6>

                            <div class="row mb-2 align-items-center">
                                <div class="col-md-2 text-end"><label class="form-label-sm fw-bold">Share & Sync</label></div>
                                <div class="col-md-4"><div class="form-check form-switch"><input type="hidden" name="share_sync" value="0"><input class="form-check-input" type="checkbox" role="switch" name="share_sync" value="1" {{ \App\Models\Setting::get('share_sync') == '1' ? 'checked' : '' }}></div></div>
                                <div class="col-md-2 text-end"><label class="form-label-sm fw-bold">Sort Names By</label></div>
                                <div class="col-md-4"><select class="form-select form-select-sm" name="sort_names"><option value="last_first" {{ \App\Models\Setting::get('sort_names') == 'last_first' ? 'selected' : '' }}>Last Name, First Name</option><option value="first_last" {{ \App\Models\Setting::get('sort_names') == 'first_last' ? 'selected' : '' }}>First Name, Last Name</option></select></div>
                            </div>

                            <div class="row mb-2 align-items-center">
                                <div class="col-md-2 text-end"><label class="form-label-sm fw-bold">App/Shared Folder</label></div>
                                <div class="col-md-10"><div class="input-group input-group-sm"><input type="text" name="shared_folder" id="folder_display" class="form-control" value="{{ \App\Models\Setting::get('shared_folder', 'D:\Documents_File\1. Important Databases') }}"><button class="btn btn-success" type="button" onclick="document.getElementById('hidden_folder_input').click();"><i class="fas fa-folder-open"></i></button><input type="file" id="hidden_folder_input" style="display: none;" onchange="document.getElementById('folder_display').value = this.value;"></div></div>
                            </div>

                            <hr class="my-3 text-secondary">

                            <div class="row mb-2 align-items-center">
                                <div class="col-md-2 text-end"><label class="form-label-sm fw-bold">% Format</label></div>
                                <div class="col-md-4"><input type="text" name="format_percent" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('format_percent', '0%') }}"></div>
                                <div class="col-md-2 text-end"><label class="form-label-sm fw-bold">Date Format</label></div>
                                <div class="col-md-4"><input type="text" name="format_date" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('format_date', 'mm/dd/yy') }}"></div>
                            </div>

                            <div class="row mb-2 align-items-center">
                                <div class="col-md-2 text-end"><label class="form-label-sm fw-bold">Time Format</label></div>
                                <div class="col-md-4"><input type="text" name="format_time" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('format_time', 'h:mm am/pm') }}"></div>
                                <div class="col-md-2 text-end"><label class="form-label-sm fw-bold">Currency Format</label></div>
                                <div class="col-md-4"><input type="text" name="format_currency" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('format_currency', '$#,##0.00') }}"></div>
                            </div>
                        </div>

                        <div class="text-end"><button type="submit" class="btn btn-success fw-bold"><i class="fas fa-save"></i> Save Settings</button></div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header text-center fw-bold" style="background-color: #C4D79B;">Common Field Format</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center mb-0" style="font-size: 0.85rem;">
                            <thead class="bg-light">
                                <tr><th style="width: 25%;">Date</th><th style="width: 25%;">Time</th><th style="width: 25%;">Percent</th><th style="width: 25%;">Currency</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>mm/dd/yy</td><td>h:mm</td><td>0%</td><td>$#,##0_);($#,##0)</td></tr>
                                <tr><td>mm/dd/yyyy</td><td>h:mm:ss</td><td>0.00%</td><td>$#,##0.00_);[Red]($#,##0.00)</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection