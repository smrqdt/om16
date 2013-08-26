  </div><!--/row-->
  <div class="clearfix"></div>
      <hr>

      <footer>
        <p>Powered by <a href="https://github.com/euleule/tapeshop">Tapeshop</a></p>
      </footer>

    </div><!--/.fluid-container-->
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="{$path}assets/js/jquery.dataTables.min.js"></script>
    <script src="{$path}assets/js/bootstrap.min.js"></script>
    <script src="{$path}assets/js/DT_bootstrap.js"></script>
    {literal}
    <script type="text/javascript">
      /* orders table initialisation */
      $(document).ready(function() {
        $('#ordersTable').dataTable();
      } );
  </script>
  
  <script>
    $(function() {
      window.updateIframe = function() {
          var h = $(window).height();
          $("[name='ticketshop']").height(h);
        }
        window.updateIframe();
        window.resize(window.updateIframe);
    });
  </script>
    {/literal}
  </body>
</html>