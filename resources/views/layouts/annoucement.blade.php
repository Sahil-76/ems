<!-- partial:partials/_settings-panel.html -->
<div class="theme-setting-wrapper open">
    <div id="settings-trigger" style="display: none"></div>
    <div id="theme-settings"   class="settings-panel">
        <i class="settings-close ti-close" id="settings-close"></i>
        <p class="settings-heading text-center" style="background-color:beige;">Announcements</p>
        @foreach ($annoucements as $annoucement)
            <div class="m-1">
                <div class="col-md-12 mt-3" style="border:0.1px solid #806e6e;border-radius:9px;">
                    <div class="form-group m-1">
                        <b>{{$annoucement->title}}</b>
                        <hr>
                            <div style="overflow-y: auto; max-height: 80px; width: 346px; height: 95px;">
                                {!!$annoucement->description!!}
                            </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<!-- partial -->
