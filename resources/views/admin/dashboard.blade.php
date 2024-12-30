管理者用ダッシュボードです。
<form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <input type="submit" value="logout" />
</form>