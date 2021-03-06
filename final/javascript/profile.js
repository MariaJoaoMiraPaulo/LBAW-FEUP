$(document).ready(function(){

    var updateName = document.getElementById('update-name');
    var updateMail = document.getElementById('update-mail');
    var updateNameButton = document.getElementById('update-name-button');
    var updateMailButton = document.getElementById('update-mail-button');

    $(".update-info").focus(function () {
        $('#update-button').show();
    });

    var options = {};
    options.ui = {
        container: "#pwd-container",
        showStatus: true,
        showProgressBar: true,
        viewports: {
            verdict: ".pwstrength_viewport_verdict"
        }
    };
    $('#new-password').pwstrength(options);

    $('#user-image-upload').click(function(){ $('#image-upload-button').trigger('click'); });

});

function updateUserInfo() {
    $.ajax({
        type:'post',
        url: '../actions/users/edit-user-info.php',
        data:  {'userName': $('#update-user-name').val(), 'userEmail': $('#update-user-email').val(), 'userCountry': $("#selected-country option:selected").val(), 'userCity': $('#update-user-city').val()},
        success: function() {
            window.location.reload();
        }

    });
}

function joinProject() {
    $.ajax({
        type:'post',
        url: '../actions/users/join-project.php',
        data:  {'userCode': $('#join-project-id').val()}, //TODO change id in the HTML
        success: function(result) {
          if(result == false) {
            alert("Invalid Code");
          } else {
            console.log(result);
            alert("Result " + result);
          }
            window.location.reload();
        }

    });
}

function createProject() {
    $.ajax({
        type:'post',
        url: '../actions/users/create-project.php',
        data:  {'projectName': $('#create-project-name').val()},
        success: function() {
            window.location.reload();
        }

    });
}

function leaveProject(projectId){
    $.ajax({
        type:'post',
        url: '../actions/users/leave-project.php',
        data:  {'projectId': projectId},
        success: function() {
            window.location.reload();
        }

    });
}
