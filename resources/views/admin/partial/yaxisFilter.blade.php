<div class="">
	<div class="btn-group" role="group" aria-label="Basic example">
	  <a href="{{ setFullUrlExcept('fi') . 'fi=d' }}" class="btn btn-default {{ Request::input('fi') == 'd' ? 'active' : '' }}">{{ trans('admin.daily') }}</a>
	  <a href="{{ setFullUrlExcept('fi') . 'fi=w' }}" class="btn btn-default {{ Request::input('fi') == 'w' ? 'active' : '' }}">{{ trans('admin.weekly') }}</a>
	  <a href="{{ setFullUrlExcept('fi') . 'fi=m'}}" class="btn btn-default {{ Request::input('fi') == 'm' ? 'active' : '' }}">{{ trans('admin.monthly') }}</a>
	</div>
</div>