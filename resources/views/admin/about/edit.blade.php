@extends('admin.layouts.master')
@section('title', 'About - Admin')
@section('maincontent')
	@component('components.breadcumb', ['fourthactive' => 'active'])
		@slot('heading')
			{{ __('About') }}
		@endslot
		@slot('menu1')
			{{ __('About') }}
		@endslot
	@endcomponent
	<div class="contentbar">
		<div class="row">
			<div class="col-md-12">
				<div class="card m-b-30">
					<div class="card-header">
						<div class="row">
							<div class="col-md-6">
								<h5 class="card-box">{{ __('adminstaticword.About') }}</h5>
							</div>
						</div>
					</div>
					<div class="card-body">
						<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
							<li class="nav-item">
								<a @if ($data->getTranslation('two_heading', 'en', false) != null) class="nav-link active"
								@else
								class="nav-link" @endif
									id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home"
									aria-selected="true">{{ __('English') }}</a>
							</li>
							<li class="nav-item">
								<a @if ($data->getTranslation('two_heading', 'en', false) != null) class="nav-link"
									@else
									class="nav-link active" @endif
									id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile"
									aria-selected="false">{{ __('Arabic') }}</a>
							</li>
						</ul>
						<div class="tab-content" id="pills-tabContent">
							<div
								@if ($data->getTranslation('two_heading', 'en', false) != null) class="tab-pane fade show active"
								@else
								class="tab-pane fade show" @endif
								id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
								@include('admin.about.edit_en')
							</div>
							<div
								@if ($data->getTranslation('two_heading', 'en', false) != null) class="tab-pane fade show"
								@else
								class="tab-pane fade show active" @endif
								id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
								@include('admin.about.edit_ar')
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

<!-- main content section ended -->
<!-- This section will contain javacsript start -->
{{-- @section('script')
	<script>
		(function($) {
			"use strict";

			$(function() {

				$('#customSwitch1').change(function() {
					if ($('#customSwitch1').is(':checked')) {
						$('#sec_one').show('fast');
					} else {
						$('#sec_one').hide('fast');
					}

				});

				$('#customSwitch2').change(function() {
					if ($('#customSwitch2').is(':checked')) {
						$('#sec_two').show('fast');
					} else {
						$('#sec_two').hide('fast');
					}

				});

				$('#customSwitch3').change(function() {
					if ($('#customSwitch3').is(':checked')) {
						$('#sec_three').show('fast');
					} else {
						$('#sec_three').hide('fast');
					}

				});

				$('#customSwitch4').change(function() {
					if ($('#customSwitch4').is(':checked')) {
						$('#sec_four').show('fast');
					} else {
						$('#sec_four').hide('fast');
					}

				});

				$('#customSwitch5').change(function() {
					if ($('#customSwitch5').is(':checked')) {
						$('#sec_five').show('fast');
					} else {
						$('#sec_five').hide('fast');
					}

				});

				$('#customSwitch6').change(function() {
					if ($('#customSwitch6').is(':checked')) {
						$('#sec_six').show('fast');
					} else {
						$('#sec_six').hide('fast');
					}

				});

			});
		})(jQuery);
	</script>
	<style>
		.image_size {
			height: 80px;
			width: 200px;
		}
	</style>
@endsection --}}
<!-- This section will contain javacsript end -->
