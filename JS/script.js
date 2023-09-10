const messageBox = $("#message-box");
let recipientUserId;
let chosenContact;
var userID;
var userInfoElement = document.getElementById("user-info");
var currentUserId = (userID = userInfoElement.getAttribute("data-user-id"));
var chatType;

function setMuteIcons() {
  // Находим все элементы с атрибутом ischatmuted
  const elements = document.querySelectorAll("[ischatmuted]");

  // Проходимся по каждому элементу
  elements.forEach((element) => {
    const isChatMuted = element.getAttribute("ischatmuted");

    // Находим элемент span с id "mute-icon" внутри текущего элемента
    const muteIcon = element.querySelector("#mute-icon");

    // Проверяем значение атрибута и устанавливаем соответствующий стиль
    if (isChatMuted === "Yes") {
      muteIcon.style.display = "block";
    } else {
      muteIcon.style.display = "none";
    }
  });
}
setMuteIcons(); // Выполняем функцию и Проставляем иконки MUTE контактам

// Обработчик события для нажатия на контакт
$(".contact").click(handleContactClick);

function handleContactClick() {
  recipientUserId = $(this).data("contact-id"); // Получаем ID контакта
  recipientGroupId = $(this).data("group-id"); // Получаем ID контакта
  chosenContact = recipientUserId ?? recipientGroupId;

  if (recipientUserId) {
    chatType = "contact";
  }
  if (recipientGroupId) {
    chatType = "group";
  }

  // Удаление класса у всех элементов
  $(".contact").removeClass("chosen");
  $(".contact").attr("is_active_chat", "No");

  // Добавление класса к текущему элементу
  $(this).addClass("chosen");
  $(this).attr("is_active_chat", "Yes");
  loadMessages(chosenContact, chatType); // Вызываем функцию для загрузки сообщений

  const $searchInput = $("#search-input");
  const $contacts = $(".contact");
  $searchInput.val("");
  $contacts.removeAttr("style");
}

function loadMessages(chosenContact, chatType) {
  $.ajax({
    type: "GET",
    url: "application/pages/load_messages.php",
    data: {
      contact_id: chosenContact,
      chat_type: chatType,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        displayMessages(response.messages);

      } else {
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
    },
  });
}

// Обработка ответа от сервера и отображение сообщений
function displayMessages(messages) {
  messageBox.empty(); // Очищаем контейнер с сообщениями

  for (let i = 0; i < messages.length; i++) {
    const message = messages[i];
    // Определение класса div в зависимости от отправителя
    var divClass =
      message.sender_id == message.currentUser_id
        ? "outcome-message"
        : "income-message";

    const htmlOutput = `
<!-- ${divClass} -->
<div class="row d-flex justify-content-${
      message.sender_id == message.currentUser_id ? "end" : "start"
    }" >
  <div class="col-7">
      <div class=" border ${divClass} rounded-3 mt-4 bg-light" message_id="${
      message.message_id
    }"> 
            
          <img src="${message.sender_avatar}" class="avatar" alt="user." />

          <span class="nameInChat align-text-center">${
            message.sender_login
          }</span><br />

          <span class="msgText">${message.message}</span>

          <span class="timestamp"><br /> ${message.timestamp}</span>

      </div>
  </div>
</div>
<!-- // ${divClass} -->
`;

    messageBox.append(htmlOutput);
  }

  scrollToBottom();
}

// поиск в контактах
document.getElementById("search-input").addEventListener("input", function () {
  let searchTerm = this.value.toLowerCase();
  let contacts = document.querySelectorAll(".contact");

  contacts.forEach(function (contact) {
    let login = contact.querySelector(".nameInList").textContent.toLowerCase();

    if (login.includes(searchTerm)) {
      contact.removeAttribute("style");
    } else {
      contact.style.display = "none";
    }
  });
});

// Выбор контактов в модальном окне. Создание группы
document.addEventListener("DOMContentLoaded", function () {
  // < поиск контактов в модальном Создание группы
  document
    .getElementById("search-input-modal")
    .addEventListener("input", function () {
      let searchTerm = this.value.toLowerCase();
      let contacts = document.querySelectorAll(".contactModal");

      contacts.forEach(function (contact) {
        let login = contact
          .querySelector(".nameInModalList")
          .textContent.toLowerCase();

        if (login.includes(searchTerm)) {
          contact.classList.add("d-flex");
          contact.removeAttribute("style");
        } else {
          contact.style.display = "none";
          contact.classList.remove("d-flex");
        }
      });
    });

  // < поиск контактов в модальном Пересылка
  document
    .getElementById("search-input-forward-modal")
    .addEventListener("input", function () {
      let searchTerm = this.value.toLowerCase();
      let contacts = document.querySelectorAll(".contactToForward");

      contacts.forEach(function (contact) {
        let login = contact
          .querySelector(".nameInModalList")
          .textContent.toLowerCase();

        if (login.includes(searchTerm)) {
          contact.classList.add("d-flex");
          contact.removeAttribute("style");
        } else {
          contact.style.display = "none";
          contact.classList.remove("d-flex");
        }
      });
    });
  // поиск контактов в модальном />

  // Выбор контактов в модальном группа
  var contactModals = document.querySelectorAll(".contactModal");
  var contactsToGroup = [];

  contactModals.forEach(function (contactModal) {
    contactModal.addEventListener("click", function () {
      var icon = contactModal.querySelector("i");
      var contactId = contactModal.getAttribute("data-contact-id");

      if (icon.style.display === "none" || icon.style.display === "") {
        icon.style.display = "block";
        contactModal.classList.replace("noChosen", "chosen");
        contactsToGroup.push(contactId);
      } else {
        icon.style.display = "none";
        contactModal.classList.replace("chosen", "noChosen");

        // Найти индекс элемента в массиве, который соответствует заданному data-contact-id
        var indexToRemove = contactsToGroup.indexOf(contactId);
        // Проверьте, найден ли элемент
        if (indexToRemove !== -1) {
          // Используйте метод splice() для удаления элемента из массива
          contactsToGroup.splice(indexToRemove, 1);
        }
      }
    });
  });
  // Выбор контактов в модальном />

  var createGroupBtn = document.getElementById("createGroup");

  createGroupBtn.addEventListener("click", function () {
    var groupName = document.getElementById("groupName").value;
    var userInfoElement = document.getElementById("user-info");
    var userID = userInfoElement.getAttribute("data-user-id");

    // Проверка массива и наличия имени группы
    if (contactsToGroup.length === 0 || groupName == "") {
      // Массив пуст
      alert(
        "Нельзя создать группу без контактов или без названия. Вам попросту не с кем будет общаться. Выберите контакты, пожалуйста"
      );
    } else {

      contactsToGroup.push(userID); // добовляем в массив создателя

      createGroup(userID, groupName, contactsToGroup);
    }
  });
});

function createGroup(userID, groupName, contactsToGroup) {
  //  объект данных для отправки на сервер
  var data = new FormData();
  data.append("groupName", groupName);
  data.append("userID", userID);
  contactsToGroup.forEach(function (contact) {
    data.append("contactsToGroup[]", contact);
  });

  // AJAX запрос на сервер
  fetch("application/pages/create_group.php", {
    method: "POST",
    body: data,
  })
    .then(function (response) {
      if (!response.ok) {
        throw new Error("Произошла ошибка HTTP, статус " + response.status);
      }
      return response.json(); 
    })
    .then(function (data) {
      // Функция, которая будет выполнена при успешном ответе от сервера
      updateGroupList(data, userID, groupName, contactsToGroup);

      $("#exampleModal").modal("hide"); // закрыть модальное окно
    })
    .catch(function (error) {
      // Функция, которая будет выполнена при ошибке
    });
}

function updateGroupList(data, userID, groupName, contactsToGroup) {
  var groupList = document.getElementById("group-list");
  newGroup =
    `<div class="row py-2 flex-row flex-wrap contact group сhosen">
    <div class="col ">
      <span data-group-id="` +
    data.createdGroupId +
    `" class="nameInList align-middle">` +
    groupName +
    `</span>
    </div>
  </div>`;

  groupList.insertAdjacentHTML("afterbegin", newGroup);
}

// Функция для автоматической прокрутки вниз
function scrollToBottom() {
  var chatBox = document.getElementById("message-container");
  chatBox.scrollTop = chatBox.scrollHeight;
}

// Добавляем обработчик нажатия клавиши Enter при отправке сообщения

var sendMsgBtn = document.getElementById("send-message");
document.addEventListener("keydown", function (event) {
  // Проверяем, является ли клавиша нажатием Enter
  if (event.key === "Enter") {
    // Проверяем, существует ли кнопка и она видима
    if (
      sendMsgBtn &&
      window.getComputedStyle(sendMsgBtn).getPropertyValue("display") !==
        "none"
    ) {
      // Выполняем действие по нажатию Enter (нажимаем кнопку)
      sendMsgBtn.click();
    }
  }
});


// Добавляем контакт
var findContactBtn = document.getElementById("findContact");

// Добавляем обработчик нажатия клавиши Enter на элементе input
document.addEventListener("keydown", function (event) {
  // Проверяем, является ли клавиша нажатием Enter
  if (event.key === "Enter") {
    // Проверяем, существует ли кнопка и она видима
    if (
      findContactBtn &&
      window.getComputedStyle(findContactBtn).getPropertyValue("display") !==
        "none"
    ) {
      // Выполняем действие по нажатию Enter (нажимаем кнопку)
      findContactBtn.click();
    }
  }
});



findContactBtn.addEventListener("click", function () {
  var input = document.getElementById("find-contact-input-modal").value;

  var data = new FormData();
  data.append("contact_to_search", input);
  data.append("action", "contact_search");

  // AJAX запрос на сервер
  fetch("application/pages/search_contact.php", {
    method: "POST",
    body: data,
  })
    .then(function (response) {
      if (!response.ok) {
        throw new Error("Произошла ошибка HTTP, статус " + response.status);
      }
      return response.json(); 
    })
    .then(function (data) {
      // Проверяем, есть ли поле "error" в ответе
      if ("error" in data) {
        // Если есть, вывести сообщение об ошибке
        showFoundContact(data);
      } else {
        // В противном случае, обработать данные, как обычно
        showFoundContact(data.FoundUser);
      }
    })
    .catch(function (error) {
      // Функция, которая будет выполнена при других ошибках
    });
});


function showFoundContact(data) {
  var addContactContainer = document.getElementById("add-contact-container");

  if (data.error != "Пользователь не найден") {
    var login = data.login || data.email;
    addContactContainer.innerHTML =
      `<div class="row d-flex justify-content-center align-items-center" data-contact-id = "` +
      data.id +
      `">
    <div class="col-auto">
        <img src="` +
      data.avatar_path +
      `" class="avatarModal" />
    </div>
    <div class="col">
        <span class="nameInModalList">` +
      login +
      `</span>
    </div>
    <div class="col-auto">
    <button type="button" class="btn btn-primary" id="addContactBtn">Добавить</button>
    </div>
    </div>`;

    addContactBtnFuncction(data.id, data.avatar_path, login);
  } else {
    addContactContainer.innerHTML =
      `<div class="row d-flex contactToForward noChosen justify-content-center align-items-center">
      <span>` +
      data.error +
      `</span>
      </div>`;
  }
}

function addContactBtnFuncction(id, avatar_path, login) {
  //Клик на добавление контакта
  var addContactBtn = document.getElementById("addContactBtn");
  addContactBtn.addEventListener("click", function () {
    $("#addContactModal").modal("hide"); // закрыть модальное окно
    document.getElementById("find-contact-input-modal").value = "";
    document.getElementById("add-contact-container").innerHTML = "";

    updateContactList(id, avatar_path, login);
  });
}

function updateContactList(id, avatar_path, login) {
  var contactList = document.getElementById("contact-list");
  var newContact =
    `
    <div class="row py-2 flex-row flex-wrap contact noChosen " contlogin="` +
    login +
    `" is_active_chat="No" data-contact-id="` +
    id +
    `" ischatmuted="No">
      <div class="col-auto ps-3 pe-0 me-2 align-self-center ">
          <div class="avatar-container">
              <img src="` +
    avatar_path +
    `" class="avatarLeftBar">
              <span id="mute-icon" style="display: none;" class="iconInAvatar"><i class="fas fa-volume-mute"></i></span> 
          </div>
      </div>
      <div class="col ps-0">
          <span class="nameInList align-middle">` +
    login +
    `</span>
      </div>
    </div>
  `;

  // Вставляем новый контакт в начало контейнера
  contactList.insertAdjacentHTML("afterbegin", newContact);

  // Получаем данные пользователя и регистрируем новй контакт в БД

  createConnection(currentUserId, id)
    .then(function (exportData) {
      // Обработка успешного ответа
      // insertGreetingMsg(exportData);
    })
    .catch(function (error) {
      // Обработка ошибки
    });

  // Получаем ссылку на новый контакт
  var newContactElement = contactList.querySelector(
    '[data-contact-id="' + id + '"]'
  );

  // Проверяем, что контакт был найден
  if (newContactElement) {
   
    // Вызываем функцию клика для нового контакта
    handleContactClick.call(newContactElement); // используем call для установки правильного контекста
  }
}



function insertGreetingMsg(currentUserData) {
  // Вставляем приветственное Сообщение
  var messageBox = document.getElementById("message-box");

  var timestamp = getTimestamp();
  var newContactMsg =
    `<div class="row d-flex justify-content-end">
    <div class="col-7">
        <div class=" border outcome-message rounded-3 mt-4 bg-light" message_id="` +
    currentUserData.last_inserted_msg_id +
    `"> 
              
            <img src="` +
    currentUserData.avatar_path +
    `" class="avatar" alt="user.">
  
            <span class="nameInChat align-text-center">` +
    currentUserData.login +
    `</span><br>
  
            <span class="msgText">Привет. Хочу добавить тебя в свой контакт лист</span>
  
            <span class="timestamp"><br> ` +
    timestamp +
    `</span>
  
        </div>
    </div>
  </div>`;
  messageBox.innerHTML = newContactMsg;
}

// Получаем данные пользователя и регистрируем новй контакт в БД
function createConnection(currentUserId, newContactId) {
  return new Promise(function (resolve, reject) {
    var data = new FormData();
    data.append("action", "create_connection");
    data.append("current_user_id", currentUserId);
    data.append("new_contact_id", newContactId);
    data.append("msg_text", "Привет. Хочу добавить тебя в свой контакт лист");
    var exportData;
    // AJAX запрос на сервер
    fetch("application/pages/search_contact.php", {
      method: "POST",
      body: data,
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error("Произошла ошибка HTTP, статус " + response.status);
        }
        return response.json();
      })
      .then(function (data) {
        // Проверяем, есть ли поле "error" в ответе
        if ("error" in data) {
          // Если есть, вывести сообщение об ошибке
        } else {
          // В противном случае, обработать данные и вызвать resolve
          var exportData = {
            login: data.current_user_data.login,
            email: data.current_user_data.email,
            avatar_path: data.current_user_data.avatar_path,
            last_inserted_msg_id: data.last_inserted_msg_id,
          };
          resolve(exportData);
        }
      })
      .catch(function (error) {
        // Функция, которая будет выполнена при других ошибках
        reject(error);
      });
  });
}

function getTimestamp() {
  // Получение текущей даты и времени в формате timestamp (миллисекунды с 1970 года)
  var timestamp = Date.now();

  // Создание объекта Date из timestamp
  var date = new Date(timestamp);

  // Получение отдельных компонентов даты и времени
  var year = date.getFullYear();
  var month = String(date.getMonth() + 1).padStart(2, "0"); // Добавляем нули перед месяцем, если он < 10
  var day = String(date.getDate()).padStart(2, "0"); // Аналогично с днем
  var hours = String(date.getHours()).padStart(2, "0");
  var minutes = String(date.getMinutes()).padStart(2, "0");
  var seconds = String(date.getSeconds()).padStart(2, "0");

  // Формируем строку в нужном формате
  var formattedDateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

  return formattedDateTime;
}
