import './component/_follow'

(() => {
    let send = XMLHttpRequest.prototype.send
    XMLHttpRequest.prototype.send = function() {
        this.addEventListener('load', function() {
            if (
              this.readyState === XMLHttpRequest.DONE
                &&
              this.status === 403
            ) {
                try {
                    let json = JSON.parse(this.responseText)

                    if (
                      'undefined' !== json.status
                      &&
                      'login_required' === json.status
                    )
                        BootstrapModal(json.title, json.body)
                } catch (e) {
                }
            }
        })

        return send.apply(this, arguments)
    }
})()
