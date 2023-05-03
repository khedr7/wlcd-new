<div class="contentbar">
	<div class="row">
		<div class="col-md-12">
			<div class="card m-b-30">
				<div class="card-header">
					<div class="row">
						<div class="col-md-6">
							<h5 class="card-box">{{ __('adminstaticword.Edit') }} {{ __('adminstaticword.Course') }}</h5>
						</div>
					</div>
				</div>
				<div class="card-body">
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a
								@if ($cor->getTranslation('title', 'en', false) != null) class="nav-link active"
                                @else
                                class="nav-link" @endif
								id="pills-english-tab" data-toggle="pill" href="#pills-english" role="tab" aria-controls="pills-english"
								aria-selected="true">{{ __('English') }}</a>
						</li>
						<li class="nav-item">
							<a
								@if ($cor->getTranslation('title', 'en', false) != null) class="nav-link"
                                @else
                                class="nav-link active" @endif
								id="pills-arabic-tab" data-toggle="pill" href="#pills-arabic" role="tab" aria-controls="pills-arabic"
								aria-selected="false">{{ __('Arabic') }}</a>
						</li>
					</ul>
					<div class="tab-content" id="pills-tabContent">
						<div
							@if ($cor->getTranslation('title', 'en', false) != null) class="tab-pane fade show active"
								@else
								class="tab-pane fade show" @endif
							    id="pills-english" role="tabpanel" aria-labelledby="pills-english-tab">
							@include('admin.course.editcor_en')
						</div>
						<div
							@if ($cor->getTranslation('title', 'en', false) != null) class="tab-pane fade show"
                            @else
                            class="tab-pane fade show active" @endif
							id="pills-arabic" role="tabpanel" aria-labelledby="pills-arabic-tab">
							@include('admin.course.editcor_ar')
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>