</body>

<script src="<?= base_url("assets/js/popper.min.js"); ?>"></script>
<script src="<?= base_url("assets/js/bootstrap.min.js"); ?>"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="<?= base_url("assets/js/number.js"); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js"></script>
<script src="<?= base_url("assets/js/footable.js"); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script src="<?= base_url("assets/js/jquery.disableAutoFill.js"); ?>"></script>

<script>
$(document).ready(function() {
	$('.table-foo').footable();
    $('a.del_qual').confirm({
        title: "Are you sure?",
        content: "You are about to delete a qualification, including all applications of this qualification with members.<br><br>Are you sure?",
    });
    $('a.member_del').confirm({
        title: "Are you sure?",
        content: "You are about to permanently delete a member.<br><br>Are you sure?",
    });
    $('.no_autofill').disableAutoFill();
});
</script>
</html>