<!DOCTYPE html>
<html lang="{{$config['language']}}">
<head>
    <!-- 加载样式及META信息 -->
    @include('admin::common/meta')
</head>
<body class="hold-transition skin-green sidebar-mini fixed @if($config['admin']['multiplenav']) multiplenav @endif" id="tabs">
<div class="wrapper">

    <!-- 头部区域 -->
    <header id="header" class="main-header">
        @include('admin::common/header')
    </header>

    <!-- 左侧菜单栏 -->
    <aside class="main-sidebar">
        @include('admin::common/menu')
    </aside>

    <!-- 主体内容区域 -->
    <div class="content-wrapper tab-content tab-addtabs">
        @if($fixedmenu)
        <div role="tabpanel" class="tab-pane {{$referermenu?'':'active'}}" id="con_{{$fixedmenu['id']}}">
            <iframe src="{{$fixedmenu['url']}}?addtabs=1" width="100%" height="100%" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling-x="no" scrolling-y="auto" allowtransparency="yes"></iframe>
        </div>
        @endif
        @if($referermenu)
        <div role="tabpanel" class="tab-pane active" id="con_{{$referermenu['id']}}">
            <iframe src="{{$referermenu['url']}}?addtabs=1" width="100%" height="100%" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling-x="no" scrolling-y="auto" allowtransparency="yes"></iframe>
        </div>
        @endif
    </div>

    <!-- 底部链接,默认隐藏 -->
    <footer class="main-footer hide">
        <div class="pull-right hidden-xs">
        </div>
        <strong>Copyright &copy; 2017-2018 <a href="https://www.fastadmin.net">Fastadmin</a>.</strong> All rights reserved.
    </footer>

    <!-- 右侧控制栏 -->
    <div class="control-sidebar-bg"></div>
    @include('admin::common/control')
</div>

<!-- 加载JS脚本 -->
@include('admin::common/script')
</body>
</html>