<script>
    window.addEventListener('openPdfInNewTab', function (data) {
        window.open('/' +'{{app()->getLocale()}}/super-admin/pdf-viewer/invoicepdf/' + data.detail.trackingId, '_blank');
    });
</script>
