// Обновление списка контактов
function reloadContactsList()
{
    let sort = $('#contactsListSort :selected').val() || 'date_desc'
    sendApiRequest({action: 'contact.list', sort}, function(response){
      $('.contacts-list-container').html(response.html)
    })
}

// Обновление списка контактов (после редактирования контакта)
function reloadContactsListAfterEdit()
{
  reloadContactsList()
  $('#universalModal').modal('hide')
}

// Показ панели управления контактом
$(document).on('click', '.contacts-list .contact', function(e){
  $('.contacts-list .contact').removeClass('show-buttons')
  $(this).addClass('show-buttons')
})

// Скрытие панели управления контактом
$(document).on('click', '.btn-hider', function(e){
  e.stopPropagation()
  $(this).closest('.contact').removeClass('show-buttons')
})

// Загрузка модального окна просмотра\удаления\правки контакта
$(document).on('click', '.btn-modal-contact', function(e){
  e.stopPropagation()
  let id = $(this).closest('.contact').data('id')
  let action = 'contact.' + $(this).data('action')

  sendApiRequest({action, id}, function(response){
    if (response.html) {
      $('#universalModal').html(response.html)
      $('#universalModal').modal('show')
    }

    if (response.error) {
      let errorDiv = $('<div class="alert alert-danger"></div>').html(response.error)
      $('#errorModal .alert').html(response.error)
      $('#errorModal').modal('show')
    }
  })
})

// Подтверждение удаления
$(document).on('click', '.btn-remove-contact-accept', function(e){
  e.preventDefault()
  $(this).attr('disabled', 'disabled')

  let id = $(this).data('id')
  let action = 'contact.remove.accept'

  sendApiRequest({action, id}, function(response){
    $('#universalModal').modal('hide')
    reloadContactsList()
  })
})

// Сортировка списка контактов
$(document).on('change', '#contactsListSort', function(e){
  $(this).attr('disabled', 'disabled')
  reloadContactsList()
})