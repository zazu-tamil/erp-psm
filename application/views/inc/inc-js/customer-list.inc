<script>
  jQuery(function ($) {
    // 🔹 Capture current location and fill inputs
    function captureLocation(modalId) {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function (position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Fill the latitude & longitude fields
            $(`${modalId} #latitude`).val(latitude);
            $(`${modalId} #longitude`).val(longitude);

            // Generate Google Maps URL
            const mapLink = `https://www.google.com/maps?q=${latitude},${longitude}`;
            $(`${modalId} #google_map_location`).val(mapLink);

            // Optional success feedback
            alert("✅ Location captured successfully!");
          },
          function (error) {
            switch (error.code) {
              case error.PERMISSION_DENIED:
                alert("❌ Permission denied. Please allow location access.");
                break;
              case error.POSITION_UNAVAILABLE:
                alert("⚠️ Location information is unavailable.");
                break;
              case error.TIMEOUT:
                alert("⏱️ Request timed out. Try again.");
                break;
              default:
                alert("Error capturing location: " + error.message);
            }
          },
          { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
      } else {
        alert("Geolocation is not supported by this browser.");
      }
    }

    // 🔹 Capture button click (Add Modal)
    $(document).on("click", "#add_modal #get-location-btn", function () {
      captureLocation("#add_modal");
    });

    // 🔹 Capture button click (Edit Modal)
    $(document).on("click", "#edit_modal #get-location-btn", function () {
      captureLocation("#edit_modal");
    });

    // 🔹 Edit record AJAX load
    $(document).on("click", ".edit_record", function () {
      let id = $(this).val();

      $.ajax({
        url: "<?php echo site_url('get-data'); ?>",
        type: "POST",
        data: { tbl: "customer_info", id: id },
        dataType: "json",
        success: function (d) {
          console.log(d);

          // ✅ Populate fields in Edit modal
          $("#edit_modal #customer_id").val(d.customer_id);
          $("#edit_modal #customer_name").val(d.customer_name);
          $("#edit_modal #contact_name").val(d.contact_name);
          $("#edit_modal #crno").val(d.crno);
          $("#edit_modal #country").val(d.country);
          $("#edit_modal #address").val(d.address);
          $("#edit_modal #mobile").val(d.mobile);
          $("#edit_modal #mobile_alt").val(d.mobile_alt);
          $("#edit_modal #email").val(d.email);
          $("#edit_modal #remarks").val(d.remarks);
          $("#edit_modal #customer_code").val(d.customer_code);
          $("#edit_modal #gst").val(d.gst);
          $("#edit_modal #latitude").val(d.latitude);
          $("#edit_modal #longitude").val(d.longitude);
          $("#edit_modal #google_map_location").val(d.google_map_location);

          // ✅ Handle status
          $(`#edit_modal input[name="status"][value="${d.status}"]`).prop(
            "checked",
            true
          );

          // ✅ Show Edit modal
          $("#edit_modal").modal("show");
        },
        error: function (xhr, status, error) {
          alert("Error fetching data: " + error);
        },
      });
    });

    // 🔹 Delete record
    $(document).on("click", ".del_record", function () {
      let id = $(this).val();
      if (confirm("Are you sure you want to delete this vendor?")) {
        $.ajax({
          url: "<?php echo site_url('delete-record'); ?>",
          type: "POST",
          data: { tbl: "customer_info", id: id },
          success: function (response) {
            alert(response);
            location.reload();
          },
          error: function (xhr, status, error) {
            alert("Error deleting record: " + error);
          },
        });
      }
    });
  });
</script>
 

<!-- Font Awesome -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
/>

<!-- jQuery UI -->
<link
  rel="stylesheet"
  href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
/>
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<!-- DataTables -->
<link
  rel="stylesheet"
  href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"
/>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable("#customer_list")) {
      $("#customer_list").DataTable().clear().destroy();
    }

    $("#customer_list").DataTable({
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      pageLength: 10,
      order: [[0, "asc"]],
    });
  });
</script>
