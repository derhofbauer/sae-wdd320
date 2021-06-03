console.log('Works! :D')

document.querySelectorAll('.favourite-add').forEach(($item) => {
  $item.addEventListener('click', (event) => {
    event.preventDefault()

    const url = $item.dataset.href

    fetch(url, {
      method: 'POST',
    })
      .then((response) => response.json())
      .then((json) => {
        const favouriteCount = json.length;

        document.querySelectorAll('.favourites-counter').forEach(($counter) => {
          $counter.textContent = favouriteCount
        })
      })

  })
})

document.querySelectorAll('.favourite-remove').forEach(($item) => {
  $item.addEventListener('click', (event) => {
    event.preventDefault();

    const url = $item.dataset.href

    fetch(url, {
      method: 'POST',
    })
      .then((response) => response.json())
      .then((json) => {
        const favouriteCount = json.length;

        document.querySelectorAll('.favourites-counter').forEach(($counter) => {
          $counter.textContent = favouriteCount
        })

        document.querySelectorAll('.favourite').forEach(($favourite) => {
          let _delete = true;
          json.forEach(($jsonItem) => {
            const classname = 'favourite-' + $jsonItem.id
            if ($favourite.classList.contains(classname)) {
              _delete = false;
            }
          })

          if (_delete === true) {
            $favourite.remove()
          }
        })
      })

  })
})
