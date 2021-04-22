function options(overlay="rgba(26, 26, 26, 0.6)"){
    return {
        settings: {
            overlayColor: overlay,
            autoplaySpeed: 1500,
            transitionSpeed: 900,
        },
        buttons: {
            showDownloadButton: false
        },
        caption: {
            showCaption: false
        },
        thumbnails: {
            thumbnailsOpacity: 0.6
        }
    }
}

module.exports = {
    options
}