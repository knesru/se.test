<form id="fnc_form">
    <div class="form">
    </div>
</form><!-- form -->
<script type="application/javascript">
    $(function(){
        let $form = $('#fnc_form');
       // $requestsGrid.pqGrid( "setColModel" ,rcm);
        setTimeout(function(){
            let rcm = $requestsGrid.pqGrid( "option", "colModel" );
            let a = rcm[9];
            rcm[9] = rcm[6];
            rcm[6] = a;
            console.log(rcm);
            $requestsGrid.pqGrid( "option" ,'colModel',  rcm).pqGrid( "refresh" );
        },1000);
        let ccm = $componentsGrid.pqGrid( "getColModel" );
    });

</script>