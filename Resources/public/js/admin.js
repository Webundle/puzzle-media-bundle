altair_form_file_upload.init("file");
altair_forms.init();
altair_md.init();

$('body').on('click', '#add_files_to_folder', function(e){
    e.preventDefault();
    var files = [];
        url = $(this).attr('href');

     $("div.icheckbox_md.checked > input").each(function(){
        var id = $(this).attr("id");
        // files.push($("#item_" + id).attr('src'));
        files.push(id);
    });

    if (files.length < 1) {
        $("#addFilesToFolder").hide();
        UIkit.modal.alert('Aucun fichier sélectionné !');
    }else {
        $.post(url, {files_to_add: files}, function(response){
            if(response.status == 1){
                $("#addFilesToFolder").hide();
                UIkit.modal.alert('Fichiers ajoutés !');
            }
        });
    }
});

$('body').on('click', '.download-folder', function(e){
    e.preventDefault();
    $.ajax({
    	method: 'GET',
    	url: $(this).attr('href'),
//    	beforeSend: function(jqXHR, settings){
//    		UIkit.notify({
//                message: 'Chargement...',
//                pos: 'top-right',
//                timeout: 2000,
////                status: 'error'
//            });
//    	},
    	success: function(response, textStatus, jqXHR){
    		window.open(response.target,'_blank');
    	},
    	error: function(jqXHR, textStatus, errorThrown){
    		UIkit.notify({
                message: jqXHR.status,
                pos: 'top-right',
                timeout: 5000,
                status: 'danger'
            });
    	},
    	complete: function(jqXHR, textStatus){
    		
    	}
    });
});

$('body').on('click', '#toggle-source', function(e){
    e.preventDefault();
    
    if ($("#source").val() == "local") {
        $('.alternate').removeClass('uk-hidden');
        $("#local").addClass('uk-hidden');
        $("#source").val('remote');
    }else {
        $('.alternate').addClass('uk-hidden');
        $("#source").val('local');
        $("#local").removeClass('uk-hidden');
    }
});


// Shared agenda
$('body').on('change', '#filter', function(e){
    $("#allowed-extensions-container").html($(this).val());
});

$("body").on('mouseenter', '.toggleable', togglize);
$("body").on('mouseleave', '.toggleable', untogglize);