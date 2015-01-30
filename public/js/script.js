/**
 * JS for generator.
 **/
var validHostnameRegex = new RegExp("^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$");

var editor, editor2;
$(document).ready(function(){
    // Ace editor configs
    editor = ace.edit("private-data");
    editor2 = ace.edit("decode-output-payload");
    editor.setOptions({
        minLines: 10,
        maxLines: 20
    });
    editor.setTheme("ace/theme/monokai");
    editor.getSession().setMode("json");
    editor.setValue(JSON.stringify({test: true}, null, '\t'));
    editor2.setOptions({
        minLines: 10,
        maxLines: 20
    });
    editor2.setTheme("ace/theme/monokai");
    editor2.getSession().setMode("json");


    // Setup Validation handling
    $('#encode-form').submit(function(e){
        validateEncode();

        e.preventDefault();
        return false;
    });
    $('#decode-form').submit(function(e){
        validateDecode();

        e.preventDefault();
        return false;
    });
});

function validateEncode(){
    var algorithmField = $('#algorithm'),
        issuerField = $('#issuer'),
        expiryField = $('#expiry'),
        secretField = $('#secret'),
        now = moment().unix();

    var supportedAlgorithms = [
        "HS256",
        "HS384",
        "HS512"
    ];

    if(!$.inArray(algorithmField.value, supportedAlgorithms))
    {
        return showError("algorithm", "Algorithm not supported");
    }

    if(issuerField.val() != "" && !validHostnameRegex.test(issuerField.val()))
    {
        return showError("issuer", "Issuer should be a valid hostname.");
    }

    if(expiryField.val() != "" && now > expiryField.val())
    {
        return showError("expiry", "Expiry timestamp must be in the future.");
    }

    if(secretField.val() == "")
    {
        return showError("secret", "This field is required.");
    }

    // Valid! Let's make a JWT!
    encode();
}

function validateDecode(){
    var tokenField = $('#token'),
        secretField = $('#decode-secret');

    if(tokenField.val() == "")
    {
        return showError("token", "This field is required.");
    }
    if(secretField.val() == "")
    {
        return showError("decode-secret", "This field is required.");
    }

    decode();
}

function encode(){
    var data = {},
        algorithmField = $('#algorithm'),
        issuerField = $('#issuer'),
        subjectField = $('#subject'),
        audienceField = $('#audience'),
        expiryField = $('#expiry'),
        nbfField = $('#nbf'),
        secretField = $('#secret'),
        privateData = editor.getValue();

    data.algorithm = algorithmField.val();
    if(issuerField.val() != "") data.issuer = issuerField.val();
    if(subjectField.val() != "") data.subject = subjectField.val();
    if(audienceField.val() != "") data.audience = audienceField.val();
    if(expiryField.val() != "") data.expiry = expiryField.val();
    if(nbfField.val() != "") data.nbf = nbfField.val();
    data.secret = secretField.val();

    data.privateData = privateData;

    // Ajax Request
    $.ajax({
        type: 'POST',
        url: 'php/encode-ajax.php',
        data: data,
        dataType: 'json',
        success: function(resp){
            if(resp.success === true)
            {
                $('#encode-output').val(resp.token);
            }
            else
            {
                showError(null, "Unknown error occurred.");
            }
        },
        error: function(resp){
            console.error(resp);
        }
    });
}

function decode(){
    var data = {},
        tokenField = $('#token'),
        secretField = $('#decode-secret');

    data.secret = secretField.val();
    data.token = tokenField.val();

    // Ajax Request
    $.ajax({
        type: 'POST',
        url: 'php/decode-ajax.php',
        data: data,
        dataType: 'json',
        success: function(resp){
            if(resp.success === true)
            {
                editor2.setValue(JSON.stringify(resp.data,null,'\t'));
            }
            else
            {
                showError(null, "Unknown error occurred.");
            }
        },
        error: function(resp){
            console.error(resp);
        }
    });
}

function showError(field, error){
    // TODO: a better error function.
    alert(error);
}