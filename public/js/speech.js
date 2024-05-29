try {
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    var recognition = new SpeechRecognition();
    var noteContent = '';
}
catch (e) {
    console.error(e);
    $('.no-browser-support').show();
    $('.app').hide();
}
recognition.onstart = function () {

}

recognition.onend = function () {
    if (noteContent != '') {
        if(noteContent.includes('task') && noteContent.length == 6)
        {
            point   =   noteContent.replace(/\s+/, "");
            $(`#${point}`).click();
        }
        else
        {
            // console.log(noteContent.length);
            if(noteContent.includes('submit') && noteContent.length == 6)
            {
                $('#dailyReport-form').submit();
            }
            else if(noteContent.includes('clear') && noteContent.length == 5)
            {
                $(`#${point}`).val('');
            }
            else if(noteContent.includes('revert') && noteContent.length == 6)
            {
                console.log(oldValue);
                $(`#${point}`).val(oldValue);
            }
            else
            {
                oldValue = $(`#${point}`).val();
                $(`#${point}`).val(noteContent);
            }
        }
    }
    noteContent = '';
    if(!$('.toggle-sound').hasClass('sound-mute'))
    {
        recognition.start();
    }
}

recognition.onerror = function (event) {
    if (event.error == 'no-speech') {
        alert('No speech was detected. Try again.');
    };
}
recognition.onresult = function (event) {

    // event is a SpeechRecognitionEvent object.
    // It holds all the lines we have captured so far.
    // We only need the current one.
    var current = event.resultIndex;

    // Get a transcript of what was said.
    var transcript = event.results[current][0].transcript;

    // Add the current transcript to the contents of our Note.
    noteContent += transcript;
}

