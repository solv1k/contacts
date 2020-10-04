// Кастомная валидация данных на фронте
function isValid(rule, value)
{
    let orule = rule
    let params = ''

    if (orule.indexOf(':') > 0) {
      let ruleArr = orule.split(':')
      orule = ruleArr[0]
      params = ruleArr[1]
    }

    switch (orule) {

      case 'required':
        return value !== undefined && value.length
        break

      case 'phone':
        return value.length === 11 && /\d{11}/.test(value) && $.inArray(value[0], [7, 8])
        break

      case 'alphaNumSpaces':
        return /^[a-zA-Zа-яА-Я0-9\s]+$/.test(value)
        break

      case 'minLength':
        return value.length >= parseInt(params)
        break

      case 'maxLength':
        return value.length <= parseInt(params)
        break

      default:
        return true
    }
}

// Асинхронная функция валидации данных
async function validate(validation)
{
  let validationResult = Object.assign({}, validation)
  validationResult.errors = {}

  $.each(validation, function(index, el){
    let rules = el.rule.split('|')
    el.isValid = true
    $(el.element).removeClass('validation-error')
    // Валидируем по каждому правилу
    $.each(rules, function(index, rule) {
      let result = isValid(rule, el.value)
      // Если валидация не прошла
      if (result == false) {
        el.isValid = false
        // Добавляем класс с ошибкой
        $(el.element).addClass('validation-error')
        // Пишем подсказку в лог
        log(el.field,'Invalid',rule)
        // Добавляем в общие ошибки валидации
        if (validationResult.errors[el.field] == undefined) {
          validationResult.errors[el.field] = []
        }
        validationResult.errors[el.field].push(rule)
      }
    })
  })

  return validationResult
}