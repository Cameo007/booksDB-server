function validateForm() {
    'use strict'
    const forms = document.querySelectorAll('.requires-validation')
    Array.from(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
}

function shidePassword(id) {
    const password = $("#" + id);
    const eye = $("#" + id + "-eye");
    if (password.attr("type") == "password") {
        password.attr("type", "text");
        eye.attr("class", "bi bi-eye-fill");
    } else if (password.attr("type") == "text") {
        password.attr("type", "password");
        eye.attr("class", "bi bi-eye-slash-fill");
    }
}


function hardClean(string) {
	return string.replace(/[^A-Za-zŽžÀ-ÿ0-9\-]+/, "").toLowerCase();
}
function clean(string) {
	return string.replace(/[^A-Za-zŽžÀ-ÿ0-9\-!§$%\/=?\^°´<>|+*~#()\[\]{}.:,; ]+/, "");
}

function ldbSignupFormValidation() {
	//Submit form validation
	const form = document.getElementById("signup-form");
	form.addEventListener('submit', function (event) {
		if (form.checkValidity()) {
			if ($("#ldbSignupUsername").val() == "" || $("#ldbSignupUsername").hasClass("is-invalid")) {
				event.preventDefault();
				event.stopPropagation();
				$('#ldbSignupUsername').removeClass('is-valid');
				$('#ldbSignupUsername').addClass('is-invalid');
			}
			
			if ($("#ldbSignupPassword").val() != $("#ldbSignupPasswordRepeat").val()) {
				event.preventDefault();
				event.stopPropagation();
				$('#ldbSignupPasswordRepeat').removeClass('is-valid');
				$('#ldbSignupPasswordRepeat').addClass('is-invalid');
			} else{
				$('#ldbSignupPasswordRepeat').removeClass('is-invalid');
				$('#ldbSignupPasswordRepeat').addClass('is-valid');
			}
		} else {
			event.preventDefault();
			event.stopPropagation();
			
			if ($("#ldbSignupUsername").val() == "" || $("#ldbSignupUsername").hasClass("is-invalid")) {
				$('#ldbSignupUsername').removeClass('is-valid');
				$('#ldbSignupUsername').addClass('is-invalid');
			} else {
				$('#ldbSignupUsername').removeClass('is-invalid');
				$('#ldbSignupUsername').addClass('is-valid');
			}
			
			if ($("#ldbSignupPassword").val() == "") {
				$('#ldbSignupPassword').removeClass('is-valid');
				$('#ldbSignupPassword').addClass('is-invalid');
			} else {
				$('#ldbSignupPassword').removeClass('is-invalid');
				$('#ldbSignupPassword').addClass('is-valid');
			}
			
			if ($("#ldbSignupPasswordRepeat").val() == "" || $("#ldbSignupPassword").val() != $("#ldbSignupPasswordRepeat").val()) {
				$('#ldbSignupPasswordRepeat').removeClass('is-valid');
				$('#ldbSignupPasswordRepeat').addClass('is-invalid');
			} else {
				$('#ldbSignupPasswordRepeat').removeClass('is-invalid');
				$('#ldbSignupPasswordRepeat').addClass('is-valid');
			}
		}
	}, false);
	
	//Change form validation
	$('#ldbSignupUsername').on('input', function() {
		ldbIsUsernameAvailable("#ldbSignupUsername")
	});
	
	$('#ldbSignupPassword').on('input', function() {
		if ($("#ldbSignupPassword").val() == "") {
			$('#ldbSignupPassword').removeClass('is-valid');
			$('#ldbSignupPassword').addClass('is-invalid');
		} else {
			$('#ldbSignupPassword').removeClass('is-invalid');
			$('#ldbSignupPassword').addClass('is-valid');
		}
			
		if ($("#ldbSignupPasswordRepeat").val() == "" || $("#ldbSignupPassword").val() != $("#ldbSignupPasswordRepeat").val()) {
			$('#ldbSignupPasswordRepeat').removeClass('is-valid');
			$('#ldbSignupPasswordRepeat').addClass('is-invalid');
		} else {
			$('#ldbSignupPasswordRepeat').removeClass('is-invalid');
			$('#ldbSignupPasswordRepeat').addClass('is-valid');
		}
	});
	$('#ldbSignupPasswordRepeat').on('input', function() {
		if ($("#ldbSignupPassword").val() == "") {
			$('#ldbSignupPassword').removeClass('is-valid');
			$('#ldbSignupPassword').addClass('is-invalid');
		} else {
			$('#ldbSignupPassword').removeClass('is-invalid');
			$('#ldbSignupPassword').addClass('is-valid');
		}
			
		if ($("#ldbSignupPasswordRepeat").val() == "" || $("#ldbSignupPassword").val() != $("#ldbSignupPasswordRepeat").val()) {
			$('#ldbSignupPasswordRepeat').removeClass('is-valid');
			$('#ldbSignupPasswordRepeat').addClass('is-invalid');
		} else {
			$('#ldbSignupPasswordRepeat').removeClass('is-invalid');
			$('#ldbSignupPasswordRepeat').addClass('is-valid');
		}
	});
}

function signInUp(action) {
	if (action == "up") {
		$("#signin").attr("style", "display: none;");
		$("#signup").attr("style", "");
	} else if (action == "in") {
		$("#signin").attr("style", "");
		$("#signup").attr("style", "display: none;");
	}
}

function ldbIsUsernameAvailable(id) {
	if ($(id).val() != "") {
		fetch("/api/lesedatenbank.php?cmd=isUsernameAvailable&name=" + $(id).val()).then(function (response) {
			response.json().then(response => {
				if (response["content"]) {
					$(id).removeClass('is-invalid');
					$(id).addClass('is-valid');
				} else {
					$(id).removeClass('is-valid');
					$(id).addClass('is-invalid');
				}
			});
		});
	} else {
		$(id).removeClass('is-valid');
		$(id).addClass('is-invalid');
	}
}

function ldbAddCat() {
	var catName = $("#ldbAddCat-catName").val();
	$.post("/api/lesedatenbank.php", 
		{
            cmd: "addCategory",
            categoryName: catName
		},
		function (response, status) {
			window.open("https://mint/lesedatenbank.php?table=" + encodeURIComponent(catName), "_self");
	});
}

function ldbEditCat() {
	var selectedOption = $('#ldbTableSelect').find(":selected");
	if ($("#ldbTableSelect").attr("style") == "" || $("#ldbTableSelect").attr("style") == null) {
		$("#ldbTableSelect-text").val(selectedOption.text());
	
		$("#ldbTableSelect").attr("style", "display: none;");
		$("#ldbTableSelect-text").attr("style", "");
		$("#ldbEditCat-button").html("<i class=\"bi bi-check\"></i>");
	} else {
		var oldName = selectedOption.text();
		var newName = $("#ldbTableSelect-text").val();

		selectedOption.text(newName)
		selectedOption.val(newName)
		selectedOption.attr("id", hardClean(newName))
		
		$("#ldbTableSelect-text").attr("style", "display: none;");
		$("#ldbTableSelect").attr("style", "");
		$("#ldbEditCat-button").html("<i class=\"bi bi-pencil-fill\"></i>");
		
		$.post("/api/lesedatenbank.php", 
			{
				cmd: "renameCategory",
				oldCategoryName: oldName,
				newCategoryName: newName
			},
			function (response, status) {
				$("#okModal-body").text(JSON.parse(response)['content'])
				var okModal = new bootstrap.Modal(document.getElementById('okModal'), {keyboard: false, focus: true});
				okModal.show();
		});
	}
}

function ldbDeleteCat() {
	var catName = $('#ldbTableSelect').find(":selected").val();
	
	$("#qModal-OK").attr("onclick", "ldbDeleteCat2()");
	$("#qModal-body").text("Sind Sie sicher, dass Sie die Kategorie \"" + catName + "\" endgültig löschen wollen?")
	var qModal = new bootstrap.Modal(document.getElementById('qModal'), {keyboard: false, focus: true});
	qModal.show();
}
function ldbDeleteCat2() {
	var catName = $('#ldbTableSelect').find(":selected").val();
	$.post("/api/lesedatenbank.php", 
		{
			cmd: "deleteCategory",
			categoryName: catName,
		},
		function (response, status) {
			window.open("https://mint/lesedatenbank.php","_self");
	});
}

function ldbAddBook() {
	var catName = $("#ldbTableSelect").find(":selected").val();
	var bookName = $("#ldbAddBook-bookName").val();
	var authorName = $("#ldbAddBook-authorName").val();
	var read = $("#ldbAddBook-read").prop("checked");
	
	$.post("/api/lesedatenbank.php", 
		{
            cmd: "addBook",
			categoryName: catName,
            bookName: bookName,
			author: authorName,
			read: read
		},
		function (response, status) {
			window.open("https://mint/lesedatenbank.php?table=" + encodeURIComponent(catName), "_self");
	});
}

function ldbEditBook(bookID) {
	$("#" + bookID).prop("readonly", false);
}

function ldbSaveBook(bookID) {
	if (!bookID.includes("read")) {
		$("#" + bookID).prop("readonly", true);
	}
	
	bookID = bookID.split("-")[0];
	
	var cat = $("#ldbTableSelect").find(":selected").val();
	var oldName = clean($("#" + bookID + "-name").attr('name'));
	var newName = clean($("#" + bookID + "-name").val());
	var author = clean($("#" + bookID + "-author").val());
	var read = $("#" + bookID + "-read").prop("checked");
	
	$.post("/api/lesedatenbank.php", 
		{
            cmd: "editBook",
			categoryName: cat,
            oldBookName: oldName,
			newBookName: newName,
			author: author,
			read: read
		},
		function (response, status) {
			window.open("https://mint/lesedatenbank.php?table=" + encodeURIComponent(catName), "_self");
	});
}

function ldbDeleteBook(bookID) {
	bookID = bookID.split("-")[0];
	
	$("#" + bookID + "-group").remove();
	
	var cat = $("#ldbTableSelect").find(":selected").val();
	var name = $("#" + bookID + "-name").val();
	
	$.post("/api/lesedatenbank.php", 
		{
            cmd: "deleteBook",
			categoryName: cat,
            name: name
		},
		function (response, status) {
			window.open("https://mint/lesedatenbank.php?table=" + encodeURIComponent(cat), "_self");
	});
}


function ldbChangeUsername() {
	$("#qiModal-OK").attr("onclick", "ldbChangeUsername2()");
	$("#qiModal-body-text").text("Bitte geben Sie ihren neuen Benutzernamen ein.");
	$("#qiModal-body-input").attr("placeholder", "Neuer Benutzername");
	$("#qiModal-body-input").on('input', function() {
		ldbIsUsernameAvailable("#qiModal-body-input")
	});
	var qiModal = new bootstrap.Modal(document.getElementById('qiModal'), {keyboard: false, focus: true});
	qiModal.show();
}
function ldbChangeUsername2() {
	if ($("#qiModal-body-input").val() != "" && $("#qiModal-body-input").hasClass("is-valid")) {
		//Hide modal
		$('#qiModal').modal('hide');
		
		$.post("/api/lesedatenbank.php", {
            cmd: "changeUsername",
			newUsername: $("#qiModal-body-input").val()
		},
		function (response, status) {
			$("#okModal-OK").attr("onclick", "window.location.href = window.location.href;");
			$("#okModal-body").text(JSON.parse(response)['content'])
			var okModal = new bootstrap.Modal(document.getElementById('okModal'), {keyboard: false, focus: true});
			okModal.show();
		});
	}
}

function ldbChangePassword() {
	//Build modal
	$("#qipModal-OK").attr("onclick", "ldbChangePassword2()");
	$("#qipModal-body-text").text("Bitte geben Sie ihr neues Passwort ein.");
	var qipModal = new bootstrap.Modal(document.getElementById('qipModal'), {keyboard: false, focus: true});
	qipModal.show();
	
	//Start validation
	ldbChangePasswordFormValidation();
}
function ldbChangePassword2() {
	if ($('#qipModal-body-password').hasClass('is-valid') && $('#qipModal-body-passwordRepeat').hasClass('is-valid')) {
		//Hide modal
		$('#qipModal').modal('hide');
		
		//Post data
		$.post("/api/lesedatenbank.php", {
			cmd: "changePassword",
			newPassword: $("#qipModal-body-password").val()
		},
		function (response, status) {
			$("#okModal-OK").attr("onclick", "window.location.href = window.location.href;");
			$("#okModal-body").text(JSON.parse(response)['content'])
			var okModal = new bootstrap.Modal(document.getElementById('okModal'), {keyboard: false, focus: true});
			okModal.show();
		});
	}
}

function ldbChangePasswordFormValidation() {
	function fullValidation() {
		if ($("#qipModal-body-password").val() == "") {
			$('#qipModal-body-password').removeClass('is-valid');
			$('#qipModal-body-password').addClass('is-invalid');
		} else {
			$('#qipModal-body-password').removeClass('is-invalid');
			$('#qipModal-body-password').addClass('is-valid');
		}
			
		if ($("#qipModal-body-passwordRepeat").val() == "" || $("#qipModal-body-password").val() != $("#qipModal-body-passwordRepeat").val()) {
			$('#qipModal-body-passwordRepeat').removeClass('is-valid');
			$('#qipModal-body-passwordRepeat').addClass('is-invalid');
		} else {
			$('#qipModal-body-passwordRepeat').removeClass('is-invalid');
			$('#qipModal-body-passwordRepeat').addClass('is-valid');
		}
	}
	
	$('#qipModal-body-password').on('input', function() {
		fullValidation();
	});
	$('#qipModal-body-passwordRepeat').on('input', function() {
		fullValidation();
	});
}

function ldbDeleteAccount() {
	$("#qModal-OK").attr("onclick", "ldbDeleteAccount2()");
	$("#qModal-body").text("Sind Sie sicher, dass Sie ihr Lesedatenbank-Konto endgültig löschen wollen?")
	var qModal = new bootstrap.Modal(document.getElementById('qModal'), {keyboard: false, focus: true});
	qModal.show();
}
function ldbDeleteAccount2() {
	$.post("/api/lesedatenbank.php", 
		{
            cmd: "deleteAccount"
		},
		function (response, status) {
			window.open("https://mint/lesedatenbank.php", "_self");
	});
}
