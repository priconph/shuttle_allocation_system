<footer class="main-footer fixed-bottom">
    {{-- <div class="float-right d-none d-sm-block">
        <b id="footerTimer"></b>
    </div>
    <strong>Shuttle Bus Allocation System</strong><span>&nbsp;version 1.0</span> --}}
    
    <div class="d-md-flex justify-content-between">
        <span><strong>Shuttle Bus Allocation System</strong>&nbsp;version 1.0</span>
        {{-- <span>© {{ date("Y") }} developed by Jannus Domingo</span> --}}
        <span class="d-md-block d-none" id="footerTimer"></span>
    </div>
</footer>

<script type="text/javascript">
	setInterval( () => {
		var now = new Date();
		$("#footerTimer").text(now.toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
	}, 1000);
</script>