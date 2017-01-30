@extends('home.layout.layout')

@section('content')
	<section class="contactus-section mv50">
		<div class="container">
			<form class="from form-horizontal" method="post" action="save-contactus">
				@include('home.layout.alert')
				<div class="form-body">
					<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
						<div class="col-md-2">
							Name
							{!! csrf_field() !!}
						</div>
						<div class="col-md-10">
							<input class="form-control" type="text" name="name"  value="{{ old('name') }}" placeholder="Name" required>
							<span class="help-block">
								{{ $errors->has('name') ? $errors->first('name') : '' }}
							</span>
						</div>
					</div>
					<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
						<div class="col-md-2">
							Email
						</div>
						<div class="col-md-10">
							<input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
							<span class="help-block">
								{{ $errors->has('email') ? $errors->first('email') : '' }}
							</span>
						</div>
					</div>
					<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
						<div class="col-md-2">
							Title
						</div>
						<div class="col-md-10">
							<input class="form-control" type="text" name="title" value="{{ old('title') }}" placeholder="Title" required>
							<span class="help-block">
								{{ $errors->has('title') ? $errors->first('title') : '' }}
							</span>
						</div>
					</div>
					<div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
						<div class="col-md-2">
							Subject
						</div>
						<div class="col-md-10">
							<textarea class="form-control" rows="7" name="subject" placeholder="Subject" required>{{ old('subject') }}</textarea>
							<span class="help-block">
								{{ $errors->has('subject') ? $errors->first('subject') : '' }}
							</span>
						</div>
					</div>			
				</div>
				<div class="form-actions">
					<button class="btn btn-info pull-right" type="submit">
						<i class="fa fa-send"></i>
						Send
					</button>
				</div>
			</form>
		</div>
	</section>
@stop