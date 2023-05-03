@extends('admin.layouts.master')
@section('title', 'Privacy Policy - Admin')
@section('maincontent')
	@component('components.breadcumb', ['fourthactive' => 'active'])
		@slot('heading')
			{{ __('Privacy Policy') }}
		@endslot
		@slot('menu1')
			{{ __('Privacy Policy') }}
		@endslot
	@endcomponent
	<div class="contentbar">
		<div class="row">
			<div class="col-md-12">
				<div class="card m-b-30">
					<div class="card-header">
						<div class="row">
							<div class="col-md-6">
                                <h5 class="card-box">{{ __('adminstaticword.PrivacyPolicy') }}</h5>
							</div>
						</div>
					</div>
					<div class="card-body">
						<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
							<li class="nav-item">
								<a @if ($items->getTranslation('policy', 'en', false) != null) class="nav-link active"
								@else
								class="nav-link" @endif
									id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home"
									aria-selected="true">{{ __('English') }}</a>
							</li>
							<li class="nav-item">
								<a @if ($items->getTranslation('policy', 'en', false) != null) class="nav-link"
									@else
									class="nav-link active" @endif
									id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile"
									aria-selected="false">{{ __('Arabic') }}</a>
							</li>
						</ul>
						<div class="tab-content" id="pills-tabContent">
							<div
								@if ($items->getTranslation('policy', 'en', false) != null) class="tab-pane fade show active"
								@else
								class="tab-pane fade show" @endif
								id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
								@include('admin.terms.policy')
							</div>
							<div
								@if ($items->getTranslation('policy', 'en', false) != null) class="tab-pane fade show"
								@else
								class="tab-pane fade show active" @endif
								id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
								@include('admin.terms.policy_ar')
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
