export const EventSourceListener = (eventSource) => {
    eventSource.addEventListener('user_follow', function(e) {
        let data = JSON.parse(e.data)
        let elements = document.querySelectorAll('.user-follow[data-user-slug="' + data['user-slug'] + '"]')

        elements.forEach(function(element) {
            if (true === data.isFollowed) {
                element.classList.replace('btn-red', 'btn-secondary')
            } else {
                element.classList.replace('btn-secondary', 'btn-red')
            }

            element.innerHTML = '<i class="' + data.ico + '"></i> ' + data.title
        })
    }, false)
}
