<?php
?>
<p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id nunc. Duis
    scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros molestie lectus, ut tempus eros
    massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in ligula suscipit aliquam. Praesent in eros
    vestibulum mi adipiscing adipiscing. Morbi facilisis. Curabitur ornare consequat nunc. Aenean vel metus. Ut posuere
    viverra nulla. Aliquam erat volutpat. Pellentesque convallis. Maecenas feugiat, tellus pellentesque pretium posuere,
    felis lorem euismod felis, eu ornare leo nisi vel felis. Mauris consectetur tortor et purus.</p>
<div id="tree" data-source="ajax" class="sampletree"></div>
<script type="application/javascript">
    $(function () {
        // using default options
        $("#tree").fancytree({
            source: {
                url: '/tasks/getTasksTree',
            },
            'lazyLoad': function (event, data) {
                console.log(data);
                data.result = $.ajax({
                    url: "/tasks/getTasksTree",
                    dataType: "json",
                    method: 'POST',
                    data: {mode: "children", parent: data.node.key},
                    cache: false
                });
            }
        });
    });
</script>