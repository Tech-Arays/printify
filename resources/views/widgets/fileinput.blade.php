@if (isset($mode) && $mode == 'image')

    <div class="js-fileinput fileinput d-b {{ ($file) ? 'fileinput-exists' : 'fileinput-new' }}" data-provides="fileinput">
        <div class="fileinput-preview thumbnail" data-trigger="fileinput">
            @if ($file)
                <img src="{{ url($file) }}" alt="" />
            @else
                <img src="{{ url('img/placeholders/placeholder-300x200.png') }}" alt="" />
            @endif
        </div>
        <div>
            <span class="{{ (isset($btnClasses) && $btnClasses) ? $btnClasses : 'btn btn-default btn-file'}}">
                <span class="fileinput-new">@lang('actions.select_file')</span>
                <span class="fileinput-exists">@lang('actions.change')</span>
                <input type="file" name="{{ $name }}" />
            </span>
            <span class="fileinput-exists">
                <a
                    href="#"
                    class="btn btn-link color-info td-n"
                    data-dismiss="fileinput">
                        <i class="fa fa-remove"></i>
                        @lang('actions.clear')
                </a>
            </span>
              
            @if($file)
                <a
                    href="#"
                    class="btn btn-danger td-n js-ajax-link js-confirm ladda-button"
                    data-text="@lang('actions.confirm_photo_delete')"
                    data-confirm-button="@lang('actions.delete')"
                    data-cancel-button="@lang('actions.cancel')"
                    data-style="zoom-out"
                    data-vess-event="ms.on.entity-attachment-removed">
                        <span class="ladda-label">
                            <i class="fa fa-trash"></i>
                        </span>
                </a>  
            @endif
        </div>
    </div>
        
@else 
    <div class="js-fileinput fileinput d-b {{ ($file) ? 'fileinput-exists' : 'fileinput-new' }}" data-provides="fileinput">
        <span class="{{ (isset($btnClasses) && $btnClasses) ? $btnClasses : 'btn btn-default btn-file'}}">
            <span class="fileinput-new">@lang('actions.select_file')</span>
            <span class="fileinput-exists">
                <span class="fileinput-filename">
                    {{ $file ? $file->name() : null }}
                </span>
            </span>
            <input type="file" name="{{ $name }}" />
        </span>
            
        @if (isset($deleteUrl) && $deleteUrl)
            <a
                href="{{ $deleteUrl }}"
                class="close fileinput-exists ml-10 va-m"
                style="float: none">
                <i class="fa fa-trash"></i>
            </a>
        @endif
    </div>
@endif
