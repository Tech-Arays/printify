<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel" style="padding-bottom:50px;">
            <div class="pull-left image">
                
            </div>
            <div class="pull-left info">
                <p>{{ Auth::user()->name }}</p>
                <a href="#">
                    <i class="fa fa-circle text-success"></i>
                    @lang('labels.online')
                </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- search form -->
        {{-- @include('admin.partials.search') --}}
        
      
        <!-- sidebar menu: : style can be found in sidebar.less -->
        
        @widget('Admin\LeftNav')
    </section>
    <!-- /.sidebar -->
</aside>
