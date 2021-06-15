console.log('WORKS! :D')

document.querySelectorAll('.favourite-add').forEach(($item) => {
  $item.addEventListener('click', (event) => {
    event.preventDefault()

    const url = $item.dataset.href

    fetch(url, {
      method: 'POST',
    })
      .then((response) => response.json())
      .then((json) => {
        const favouriteCount = json.length

        document.querySelectorAll('.favourites-counter').forEach(($counter) => {
          $counter.textContent = favouriteCount
        })
      })

  })
})

document.querySelectorAll('.favourite-remove').forEach(($item) => {
  $item.addEventListener('click', (event) => {
    event.preventDefault()

    const url = $item.dataset.href
    const $target = event.target

    fetch(url, {
      method: 'POST',
    })
      .then((response) => response.json())
      .then((json) => {
        const favouriteCount = json.length

        document.querySelectorAll('.favourites-counter').forEach(($counter) => {
          $counter.textContent = favouriteCount
        })
      })

    const $tr = $target.closest('tr')
    $tr.remove()
  })
})

document.querySelectorAll('.editor').forEach($item => {
  ClassicEditor
    .create($item)
    .catch(error => {
      console.error(error)
    })
})
