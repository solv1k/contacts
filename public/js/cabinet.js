$.ajaxSetup({ cache: false })

// Внутреннее логирование в консоль (можно отключить, по желанию администратора)
function log()
{
  console.log(...arguments)
}

// Отправка API-запроса с файлами и FormData
function sendDataApiRequest(data, onSuccess, hideAlerts) {
  if (hideAlerts) {
    $('.alert-sys').hide()
  }

  $.ajax({
    url: api_url,
    type: 'POST',
    processData: false,
    dataType: false,
    contentType: false,
    data,
    success: onSuccess
  })
}

// Отправка API-запроса в формате JSON
function sendApiRequest(data, onSuccess, hideAlerts) {
  if (hideAlerts) {
    $('.alert-sys').hide()
  }

  $.post(api_url, data, onSuccess, 'json')
}

// Показ системного сообщения info/success/error
function showSysAlert(type, html)
{
  $('.alert-sys').hide()
  $(`#${type} .html`).html(html)
  $(`#${type}`).show()
}

// Показ системного сообщения info
function showInfo(html) {
  showSysAlert('info', html)
}

// Показ системного сообщения success
function showSuccess(html) {
  showSysAlert('success', html)
}

// Показ системного сообщения error (фронтенд)
function showFrontError(error) {
  html = 'Ошибка валидации: '

  if (error.validation !== undefined) {
    let first_key = Object.keys(error.validation)[0]
    html +=  `${error.validation[first_key]}`
  }

  showSysAlert('error', html)
}

// Показ системного сообщения error (бэкэнд)
function showBackError(error) {
  html = 'Ошибка Ajax-запроса'

  if (error.required !== undefined) {
    html +=  `: [${error.required}] обязательно для заполнения`
  }

  if (error.wrong !== undefined) {
    html +=  `: [${error.wrong}] передан неверно или отсутвует`
  }

  if (error.validation !== undefined) {
    let first_key = Object.keys(error.validation)[0]
    html +=  `: ${error.validation[first_key]}`
  }

  if (error.upload !== undefined) {
    let first_key = Object.keys(error.upload)[0]
    html +=  `: Upload ${error.upload[first_key]}`
  }

  showSysAlert('error', html)
}

// Сброс данных формы
function resetFormFields(form)
{
  form.find('input').val('')
  form.find('.photo-preview').hide()
}


// Загрузка изображения в превью
$(document).on('change', '[data-image-container]', function(e){
  let imageContainer = $($(this).data('image-container') || '#imageContainer')
  // Если браузер поддерживает FileReader
  if (FileReader && this.files && this.files.length) {
    var fr = new FileReader();
    fr.onload = function () {
      imageContainer.find('img').attr('src', fr.result)
      imageContainer.show()
    }
    fr.readAsDataURL(this.files[0])
  }
})

// Отправка AJAX-формы
$(document).on('click', '.btn-send-ajax-form', function(e){
  e.preventDefault()

  let form = $(this).closest('form')
  let action = form.data('action')
  let onSuccessFunctionName = form.data('on-success')
  let params = {}
  let labels = {}
  let files = {}
  let validation = {}

  form.find('input[type="hidden"]').each(function(index, el) {
    let element = $(el)
    let field = element.attr('name')
    let value = element.val()
    params[field] = value
  })

  form.find('input[type="text"]').each(function(index, el) {
    let element = $(el)
    let field = element.attr('name')
    let value = element.val()
    let label = element.data('label')
    let rule = element.data('validate')
    params[field] = value
    labels[field] = label
    validation[field] = {field, rule, value, element}
  })

  form.find('input[type="file"]').each(function(index, el) {
    let element = $(el)
    let field = element.attr('name')
    let file = element.prop('files')[0]
    let label = element.data('label')
    files[field] = {field, file}
    labels[field] = label
  })

  validate(validation).then(function(validationResult){
    let errorsKeys = Object.keys(validationResult.errors || {})
    // Если валидация не прошла, показываем ошибку
    if (errorsKeys.length > 0) {
      showFrontError({validation: validationResult.errors})
      return false
    }

    // Подготавливаем данные формы
    formData = new FormData()
    formData.append('action', action);
    formData.append('params', JSON.stringify(params));
    formData.append('labels', JSON.stringify(labels));
    // Добавляем файлы
    if (Object.keys(files).length) {
      $.each(files, function(index, el) {
        formData.append(`files[${el.field}]`, el.file)
      });
    }

    sendDataApiRequest(formData, function(response){
      // Если данные формы отправлены успешно
      if (response.success !== undefined) {
          // Сбрасываем данные
          resetFormFields(form)
          // Показываем сообщение об успешной операции
          showSuccess(response.success)
          // Если есть функция On-Success, выполняем
          if (onSuccessFunctionName in window) {
            window[onSuccessFunctionName]()
          }
      }

      if (response.info !== undefined) {
          showInfo(response.info)
      }

      if (response.error !== undefined) {
          showBackError(response.error)
      }
    }, true)
  })
})