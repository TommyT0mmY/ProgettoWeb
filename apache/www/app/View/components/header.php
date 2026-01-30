<?php 
/** 
 * @var string $userId 
*/ 
?>
<header>
    <button id="open-sidebar-button" aria-label="Open navigation menu">
        <img class="menu-container" src="/images/icons/menu.svg" alt="" />
    </button>
    <a href="/" class="title-with-logo" aria-label="Go to homepage">
        <img src="/images/icons/logo.png" alt="UniboStu Logo" />          
        <h1>UniboStu</h1>
    </a>
    <a href="/users/<?= htmlspecialchars($userId) ?>" aria-label="Go to user profile">
        <img class="profile-container" src="/images/icons/user-profile.svg" alt="" />
    </a>    
 </header>
