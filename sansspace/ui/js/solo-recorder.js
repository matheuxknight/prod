/**
 * Created by Matheux on 7/23/15.
 */

//Initializing template
//For now, just one single template, but may expand depending on needs


//Initializing AudioRecord object
var AudioRecord = {
    _parentId: '',
	_parentName: '',
    $el: null,
	_recording: null,
	_description: '',
	_name: '',

    init: function (container, parentId, parentName) {
        this._parentId = parentId;
		this._parentName = parentName;
        this.$el = $(container);
		
		navigator.getUserMedia = navigator.getUserMedia ||
		navigator.webkitGetUserMedia ||
		navigator.mozGetUserMedia ||
		navigator.msGetUserMedia;

		window.AudioContext = window.AudioContext ||
		window.webkitAudioContext ||
		window.mozAudioContext ||
		window.msAudioContext;

		if (navigator.getUserMedia) {
			navigator.getUserMedia({
				audio: true
			}, _.bind(function (stream) {
				// success
				this._audioContext = new window.AudioContext;
				var source = this._audioContext.createMediaStreamSource(stream);
				_recorder = new Recorder(source, {
					workerPath: '/bower_components/Recorderjs/recorderWorker.js',
					numChannels: 1
				});
			}, this), _.bind(function () {
				// error
			}, this));
		} else {
			alert('Unfortunately, your browser does not support our recording feature, which is present within this quiz. Please use Google Chrome, or Mozilla Firefox as your main browser while using the Learning Site in order to complete this quiz and those like it.');
		}
		
		this._render();
    },

    _render: function () {
	
		$('<span class="header-right"><button data-role="attempt-submit"><i class="fa fa-check"></i> Submit</button>&nbsp;<button data-role="attempt-cancel"><i class="fa fa-times"></i> Cancel</button></span>')
			.appendTo(this.$el);
		
		$('<h3 data-role="parent-name"></h3>')
			.appendTo(this.$el);
		
		$('<div class="form-group"><div class="col-sm-12"><input type="text" class="form-control" data-role="record-name" placeholder="Name Your Recording (Required)" required></div></div>')
			.appendTo(this.$el);
		
		$('<div class="form-group"><div class="col-sm-12"><textarea rows=4 class="form-control" data-role="record-description" placeholder="Recording Description (Optional)"></textarea></div></div>')
			.appendTo(this.$el);
			
		$('<div data-role="question-reply"><button data-role="audio-record-control" data-control-type="start"><i class="fa fa-microphone"></i> Start Record</button><button data-role="audio-record-control" data-control-type="stop" disabled><i class="fa fa-microphone"></i> Stop Record</button><div data-role="preview-container"></div></div>')
			.appendTo(this.$el);
			
        //Render Parent Name Header
		this._renderParentName();

        // Bind events
        this.$el.on('click', '[data-role="audio-record-control"]', _.bind(this._onAudioRecordControlClick, this));
        this.$el.on('click', '[data-role="attempt-cancel"]', _.bind(this._onAttemptCancelClick, this));
        this.$el.on('click', '[data-role="attempt-submit"]', _.bind(this._onAttemptSubmitClick, this));
		
		// Check for getUserMedia()
		
    },

    _renderParentName: function () {
        this.$el.find('[data-role="parent-name"]').empty().html(this._parentName);
	},

    _onAudioRecordControlClick: function (event) {
		event.preventDefault();
		switch ($(event.target).attr('data-control-type')) {
			case 'start':
				// clear any recordings and start new recording
				_recorder.clear();
				_recorder.record();
				
				// set stop button as active button
				$('[data-role="audio-record-control"]').attr('disabled', true);
				$('[data-role="audio-record-control"][data-control-type="stop"]').attr('disabled', false);
				
				break;
			case 'stop':
				// stop recording
				_recorder.stop();
				
				// disable button
				$('[data-role="audio-record-control"]').attr('disabled', true);
				
				// export sound blob to html5 player created within preview container
				_recorder.exportWAV(_.bind(function (soundBlob) {
					$('[data-role="preview-container"]').empty().append(
						$('<audio>')
							.attr('src', URL.createObjectURL(soundBlob))
							.attr('controls', 'controls')
					);
					
					// set private _recording to soundBlob for export purposes
					this._recording = soundBlob;
					
					// set button states
					$('[data-role="audio-record-control"]').attr('disabled', false);
					$('[data-role="audio-record-control"][data-control-type="stop"]').attr('disabled', true);
				}, this));
				break;
		}
    },

    _onAttemptSubmitClick: function (event) {
		// Only proceed if user has input a value for recording name. If not the form validation will kick in and tell the user to input value.
        if($('[data-role="record-name"]').val()){
			event.preventDefault();
			
			// if recording has been made
			if(this._recording){
				// create new formData with proper variables
				var fd = new FormData();
				fd.append('record', this._recording);
				fd.append('parentid', this._parentId);
				fd.append('name', $('[data-role="record-name"]').val());
				fd.append('description', $('[data-role="record-description"]').val());
				
				// send form data to internalsave function for processing
				$.ajax({
					type: 'POST',
					url: '/recorder/internalsave',
					data: fd,
					processData: false,
					contentType: false
				})
				
				// reload window back to original parent
				window.location.href = '/object?id=' + this._parentId;
			}
			
			// if recording has not been set, then alert the user
			else{
				alert('Recording needed to submit!');
			}
		}
    },

    _onAttemptCancelClick: function (event) {
        event.preventDefault();
		if (confirm('Are you sure you want to cancel your recording?')) {
			window.location.href = '/object?id=' + this._parentId;
		}
    },

};