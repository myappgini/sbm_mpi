function getMpi(data, nav = true, mpi = true) {

    $j.ajax({
        url: 'hooks/mpi/mpi_AJAX.php',
        method: 'post',
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data,
        success: function (res) {
            res.message.length >0 && console.log(res.message);
            if (res.image !== null && res.image !== '') {
                const date = new Date();
                const img_url = `images/mpi/${ res.image }?=${ date.getTime() }`;

                if (nav) {
                    $j('.imagebar').remove();
                    if (res.default) {
                        const $a = $j('<a />', {
                            href: "membership_profile.php",
                            class: "navbar-link"
                        });
                        $j('a[href="membership_profile.php"]').closest('p.navbar-text').html($a);
                    }
                    $j('a[href="membership_profile.php"]').append(`<img src="${ img_url }" class="mpi-header-avatar imagebar">`);

                }
                if (mpi) {
                    $j('#mpimage').empty();
                    $j('#mpimage').append(`<img src="${ img_url }" class="img-rounded img-responsive imagebar">`);
                    if (res.default) $j('#remove-default').prop('checked', true);
                }
            }
        }
    });

}

$j(function () {
    const cl = window.location.pathname;
    if (cl.search('membership_profile.php') != -1) {
        $j('div.row>div.col-md-6:last-child').prepend(' <div id="imageProfile"></div>');
        $j('#imageProfile').load("hooks/mpi/templates/mpi_template.html");
    }

});