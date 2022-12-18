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
	return string.replace(/[^A-Za-zŽžÀ-ÿ0-9\-!§$%\/=?°<>|+*~#()\[\]{}.:,; ]+/, "");
}



function bdbSignupFormValidation() {
	//Submit form validation
	const form = document.getElementById("signup-form");
	form.addEventListener('submit', function(event) {
		if (form.checkValidity()) {
			if ($("#bdbSignupUsername").val() == "" || $("#bdbSignupUsername").hasClass("is-invalid")) {
				event.preventDefault();
				event.stopPropagation();

				$('#bdbSignupUsername').removeClass('is-valid');
				$('#bdbSignupUsername').addClass('is-invalid');
			}

			if ($("#bdbSignupPassword").val() != $("#bdbSignupPasswordRepeat").val()) {
				event.preventDefault();
				event.stopPropagation();

				$('#bdbSignupPasswordRepeat').removeClass('is-valid');
				$('#bdbSignupPasswordRepeat').addClass('is-invalid');
			} else {
				$('#bdbSignupPasswordRepeat').removeClass('is-invalid');
				$('#bdbSignupPasswordRepeat').addClass('is-valid');
			}
		} else {
			event.preventDefault();
			event.stopPropagation();

			if ($("#bdbSignupUsername").val() == "" || $("#bdbSignupUsername").hasClass("is-invalid")) {
				$('#bdbSignupUsername').removeClass('is-valid');
				$('#bdbSignupUsername').addClass('is-invalid');
			} else {
				$('#bdbSignupUsername').removeClass('is-invalid');
				$('#bdbSignupUsername').addClass('is-valid');
			}

			if ($("#bdbSignupPassword").val() == "") {
				$('#bdbSignupPassword').removeClass('is-valid');
				$('#bdbSignupPassword').addClass('is-invalid');

			} else {
				$('#bdbSignupPassword').removeClass('is-invalid');
				$('#bdbSignupPassword').addClass('is-valid');
			}

			if ($("#bdbSignupPasswordRepeat").val() == "" || $("#bdbSignupPassword").val() != $("#bdbSignupPasswordRepeat").val()) {
				$('#bdbSignupPasswordRepeat').removeClass('is-valid');
				$('#bdbSignupPasswordRepeat').addClass('is-invalid');
			} else {
				$('#bdbSignupPasswordRepeat').removeClass('is-invalid');
				$('#bdbSignupPasswordRepeat').addClass('is-valid');
			}
		}
	}, false);

	
	//Change form validation
	$('#bdbSignupUsername').on('input', function() {
		bdbIsUsernameAvailable("#bdbSignupUsername")
	});

	$('#bdbSignupPassword').on('input', function() {
		if ($("#bdbSignupPassword").val() == "") {
			$('#bdbSignupPassword').removeClass('is-valid');
			$('#bdbSignupPassword').addClass('is-invalid');
		} else {
			$('#bdbSignupPassword').removeClass('is-invalid');
			$('#bdbSignupPassword').addClass('is-valid');
		}

		if ($("#bdbSignupPasswordRepeat").val() == "" || $("#bdbSignupPassword").val() != $("#bdbSignupPasswordRepeat").val()) {
			$('#bdbSignupPasswordRepeat').removeClass('is-valid');
			$('#bdbSignupPasswordRepeat').addClass('is-invalid');
		} else {
			$('#bdbSignupPasswordRepeat').removeClass('is-invalid');
			$('#bdbSignupPasswordRepeat').addClass('is-valid');
		}
	});

	$('#bdbSignupPasswordRepeat').on('input', function() {
		if ($("#bdbSignupPassword").val() == "") {
			$('#bdbSignupPassword').removeClass('is-valid');
			$('#bdbSignupPassword').addClass('is-invalid');
		} else {
			$('#bdbSignupPassword').removeClass('is-invalid');
			$('#bdbSignupPassword').addClass('is-valid');
		}

		if ($("#bdbSignupPasswordRepeat").val() == "" || $("#bdbSignupPassword").val() != $("#bdbSignupPasswordRepeat").val()) {
			$('#bdbSignupPasswordRepeat').removeClass('is-valid');
			$('#bdbSignupPasswordRepeat').addClass('is-invalid');
		} else {
			$('#bdbSignupPasswordRepeat').removeClass('is-invalid');
			$('#bdbSignupPasswordRepeat').addClass('is-valid');
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

function bdbIsUsernameAvailable(id) {
	if ($(id).val() != "") {
		fetch("/api/bdb.php/account/isUsernameAvailable?name=" + $(id).val()).then(function(response) {
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



function bdbAddCat() {
	var catName = clean($("#bdbAddCat-catName").val());
	window.open("https://mint.jojojux.de/bdb.php?table=" + encodeURIComponent(catName), "_self");
}

function bdbEditCat() {
	var selectedOption = $('#bdbTableSelect').find(":selected");
	if ($("#bdbTableSelect").attr("style") == "" || $("#bdbTableSelect").attr("style") == null) {
		$("#bdbTableSelect-text").val(selectedOption.text());

		$("#bdbTableSelect").attr("style", "display: none;");
		$("#bdbTableSelect-text").attr("style", "");

		$("#bdbEditCat-button").html("<i class=\"bi bi-check\"></i>");
	} else {
		var oldName = selectedOption.text();
		var newName = clean($("#bdbTableSelect-text").val());

		selectedOption.text(newName)
		selectedOption.val(newName)
		selectedOption.attr("id", hardClean(newName))

		$("#bdbTableSelect-text").attr("style", "display: none;");
		$("#bdbTableSelect").attr("style", "");
		$("#bdbEditCat-button").html("<i class=\"bi bi-pencil-fill\"></i>");

		$.post("/api/bdb.php/category/rename",
			{
				oldCategoryName: oldName,
				newCategoryName: newName
			},
			function(response, status) {
				$("#okModal-body").text(JSON.parse(response)['content'])
				var okModal = new bootstrap.Modal(document.getElementById('okModal'), {
					keyboard: false,
					focus: true
				});
				okModal.show();
			});
	}
}

function bdbDeleteCat() {
	var catName = $('#bdbTableSelect').find(":selected").val();

	$("#qModal-OK").attr("onclick", "bdbDeleteCat2()");
	$("#qModal-body").text("Sind Sie sicher, dass Sie die Kategorie \"" + catName + "\" endgültig löschen wollen?");

	var qModal = new bootstrap.Modal(document.getElementById('qModal'), {
		keyboard: false,
		focus: true
	});
	qModal.show();
}

function bdbDeleteCat2() {
	var catName = $('#bdbTableSelect').find(":selected").val();
	$.post("/api/bdb.php/category/delete",
		{
			categoryName: catName,
		},
		function(response, status) {
			window.open("https://mint.jojojux.de/bdb.php", "_self");
		});
}

function bdbAddBook() {
	var catName = $("#bdbTableSelect").find(":selected").val();
	var bookName = clean($("#bdbAddBook-bookName").val());
	var authorName = clean($("#bdbAddBook-authorName").val());
	var read = $("#bdbAddBook-read").prop("checked");

	$.post("/api/bdb.php/book/add",
		{
			categoryName: catName,
			bookName: bookName,
			author: authorName,
			read: read
		},
		function(response, status) {
			window.open("https://mint.jojojux.de/bdb.php?table=" + encodeURIComponent(catName), "_self");
		});
}

function bdbEditBook(bookID) {
	$("#" + bookID).prop("readonly", false);
}

function bdbSaveBook(bookID) {
	if (!bookID.includes("read")) {
		$("#" + bookID).prop("readonly", true);
	}

	bookID = bookID.split("-")[0];

	var cat = $("#bdbTableSelect").find(":selected").val();
	var oldName = clean($("#" + bookID + "-name").attr('name'));
	var newName = clean($("#" + bookID + "-name").val());
	var author = clean($("#" + bookID + "-author").val());
	var read = $("#" + bookID + "-read").prop("checked");

	$.post("/api/bdb.php/book/edit",
		{
			categoryName: cat,
			oldBookName: oldName,
			newBookName: newName,
			author: author,
			read: read
		},
		function(response, status) {
			window.open("https://mint.jojojux.de/bdb.php?table=" + encodeURIComponent(cat), "_self");
		});
}

function bdbDeleteBook(bookID) {
	bookID = bookID.split("-")[0];

	var cat = $("#bdbTableSelect").find(":selected").val();
	var name = $("#" + bookID + "-name").val();
	
	$("#" + bookID + "-group").remove();

	$.post("/api/bdb.php/book/delete",
		{
			categoryName: cat,
			bookName: name
		},
		function(response, status) {
			window.open("https://mint.jojojux.de/bdb.php?table=" + encodeURIComponent(cat), "_self");
		});
}

function bdbChangeUsername() {
	$("#qiModal-OK").attr("onclick", "bdbChangeUsername2()");
	$("#qiModal-body-text").text(langmng.get("tools.booksDB.enterNewUsername", langmng.current));
	$("#qiModal-body-input").attr("placeholder", langmng.get("tools.booksDB.newUsername", langmng.current));

	$("#qiModal-body-input").on('input', function() {
		bdbIsUsernameAvailable("#qiModal-body-input")
	});

	var qiModal = new bootstrap.Modal(document.getElementById('qiModal'), {
		keyboard: false,
		focus: true
	});
	qiModal.show();
}

function bdbChangeUsername2() {
	if ($("#qiModal-body-input").val() != "" && $("#qiModal-body-input").hasClass("is-valid")) {
		//Hide modal
		$('#qiModal').modal('hide');

		$.post("/api/bdb.php/account/changeUsername", {
				newUsername: $("#qiModal-body-input").val()
			},
			function(response, status) {
				$("#okModal-OK").attr("onclick", "window.location.href = window.location.href;");
				$("#okModal-body").text(JSON.parse(response)['content'])

				var okModal = new bootstrap.Modal(document.getElementById('okModal'), {
					keyboard: false,
					focus: true
				});
				okModal.show();
			
				//Clear input field
				$("#qiModal-body-input").val("")
			});
	}
}

function bdbChangePassword() {
	//Build modal
	$("#qipModal-OK").attr("onclick", "bdbChangePassword2()");
	$("#qipModal-body-text").text(langmng.get("tools.booksDB.enterNewPassword", langmng.current));
	$("#qipModal-body-password").attr("placeholder", langmng.get("tools.booksDB.newPassword", langmng.current));
	$("#qipModal-body-passwordRepeat").attr("placeholder", langmng.get("tools.booksDB.newPasswordRepeat", langmng.current));
	
	var qipModal = new bootstrap.Modal(document.getElementById('qipModal'), {
		keyboard: false,
		focus: true
	});
	qipModal.show();

	//Start validation
	bdbChangePasswordFormValidation();
}

function bdbChangePassword2() {
	if ($('#qipModal-body-password').hasClass('is-valid') && $('#qipModal-body-passwordRepeat').hasClass('is-valid')) {
		//Hide modal
		$('#qipModal').modal('hide');

		//POST data
		$.post("/api/bdb.php/account/changePassword", {
				newPassword: $("#qipModal-body-password").val()
			},
			function(response, status) {
				$("#okModal-OK").attr("onclick", "window.location.href = window.location.href;");
				$("#okModal-body").text(JSON.parse(response)['content'])

				var okModal = new bootstrap.Modal(document.getElementById('okModal'), {
					keyboard: false,
					focus: true
				});
				okModal.show();
			
				//Clear input fields
				$("#qipModal-body-password").val("");
				$("#qipModal-body-functionRepeat").val("");
			});
	}
}

function bdbChangePasswordFormValidation() {
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



function bdbDeleteAccount() {
	$("#qModal-OK").attr("onclick", "bdbDeleteAccount2()");
	$("#qModal-body").text(langmng.get("tools.booksDB.wantToDeleteAccount", langmng.current));

	var qModal = new bootstrap.Modal(document.getElementById('qModal'), {
		keyboard: false,
		focus: true
	});
	qModal.show();
}

function bdbDeleteAccount2() {
	$.post("/api/bdb.php/account/delete",
		{},
		function(response, status) {
			window.open("https://mint.jojojux.de/bdb.php", "_self");
		});
}

function bdbShareCat() {
	var categoryName = $("#bdbTableSelect").find(":selected").val();
	
	$.get("/api/bdb.php/book/getAll", {
		categoryName: categoryName
	},
	function(response, status) {
		var allShared = true;
		var books = JSON.parse(response)["content"];
	
		books.forEach(function (book, index) {
			if (book["key"] == "") {
				allShared = false;
			}
		});

		if (allShared == false) {
			//POST data
			$.post("/api/bdb.php/category/startSharing", {
				categoryName: categoryName
			},
			function(response, status) {
				$(`#bdbShareCat-button`).addClass('active');
				books.forEach(function (book, index) {
					var hardCleanedBookName = hardClean(book["bookName"])
					$(`#${hardCleanedBookName}-group`).attr("key", JSON.parse(response)["key"]);
					$(`#${hardCleanedBookName}-share`).addClass('active');
				});

				$("#okModal-body").html(JSON.parse(response)['content']);

				var okModal = new bootstrap.Modal(document.getElementById('okModal'), {
					keyboard: false,
					focus: true
				});
				okModal.show();
			});
		} else {
			$("#qModal-OK").attr("onclick", `bdbStopSharingCategory("${categoryName}")`);
			$("#qModal-body").text(langmng.get("tools.booksDB.wantToStopSharing", langmng.current));

			var qModal = new bootstrap.Modal(document.getElementById('qModal'), {
				keyboard: false,
				focus: true
			});
			qModal.show();
		}
	});
}

function bdbStopSharingCategory() {
	//POST data
	$.post("/api/bdb.php/category/stopSharing", {
		categoryName: categoryName
	},
	function(response, status) {
		$(`#bdbShareCat-button`).removeClass('active');
		books.forEach(function (book, index) {
			var hardCleanedBookName = hardClean(book["bookName"]);
			$(`#${hardCleanedBookName}-group`).attr("key", "");
			$(`#${hardCleanedBookName}-share`).removeClass('active');
		});

		$('#qModal').modal('hide');

		$("#okModal-body").html(JSON.parse(response)['content']);

		var okModal = new bootstrap.Modal(document.getElementById('okModal'), {
			keyboard: false,
			focus: true
		});
		okModal.show();
	});
}

function bdbShareBook(hardCleanedBookName) {
	var categoryName = $("#bdbTableSelect").find(":selected").val();
	var bookName = $(`#${hardCleanedBookName}-name`).val();

	if ($(`#${hardCleanedBookName}-group`).attr("key") == "") {
		//POST data
		$.post("/api/bdb.php/book/startSharing", {
			categoryName: categoryName,
			bookName: bookName
		},
		function(response, status) {
			$(`#${hardCleanedBookName}-group`).attr("key", JSON.parse(response)["key"]);
			$(`#${hardCleanedBookName}-share`).addClass('active');
			
			$("#okModal-body").html(JSON.parse(response)['content']);

			var okModal = new bootstrap.Modal(document.getElementById('okModal'), {
				keyboard: false,
				focus: true
			});
			okModal.show();
		});
	} else {
		$("#qModal-OK").attr("onclick", `bdbStopSharingBook("${categoryName}", "${bookName}", "${hardCleanedBookName}")`);
		$("#qModal-body").text(langmng.get("tools.booksDB.wantToStopSharing", langmng.current));

		var qModal = new bootstrap.Modal(document.getElementById('qModal'), {
			keyboard: false,
			focus: true
		});
		qModal.show();
	}
	
	$.get("/api/bdb.php/book/getAll", {
		categoryName: categoryName
	},
	function(response, status) {
		var allShared = true;
		var books = JSON.parse(response)["content"];
	
		books.forEach(function (book, index) {
			if (book["key"] == "") {
				allShared = false;
			}
		});
		
		if (allShared) {
			$(`#bdbShareCat-button`).addClass('active');
		} else {
			$(`#bdbShareCat-button`).removeClass('active');
		}
	});
}
function bdbStopSharingBook(categoryName, bookName, hardCleanedBookName) {
	//POST data
	$.post("/api/bdb.php/book/stopSharing", {
		categoryName: categoryName,
		bookName: bookName
	},
	function(response, status) {
		$(`#${hardCleanedBookName}-group`).attr("key", "");
		$(`#${hardCleanedBookName}-share`).removeClass('active');
			
		$('#qModal').modal('hide');
			
		$("#okModal-body").html(JSON.parse(response)['content']);

		var okModal = new bootstrap.Modal(document.getElementById('okModal'), {
			keyboard: false,
			focus: true
		});
		okModal.show();
	})
}


function bdbShowShareLink() {
	$("#okModal-body").html(langmng.get("tools.booksDB.showShareLink", langmng.current).replace("${url}", window.location));

	var okModal = new bootstrap.Modal(document.getElementById('okModal'), {
		keyboard: false,
		focus: true
	});
	okModal.show();
	
}
