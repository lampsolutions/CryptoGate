<div class="mdl-card__actions" style="text-align: left">
    <footer class="mdl-mini-footer" style="padding: 0; background-color: inherit;">
        <div class="mdl-mini-footer--left-section" style="margin: 0 auto;">
            <ul class="mdl-mini-footer--link-list">
                @if(!empty(Cache::get('agb')))
                    <li><a href="/agb">AGB</a></li>
                @endif

                <li><a href="/datenschutz">Datenschutz</a></li>

                <li><a href="/impressum">Impressum</a></li>

            </ul>
            <div class="mdl-mini-footer--link-list" style="display: block !important; text-align: center; font-size:14px;">
                    <a target="_blank" href="https://www.cryptopanel.de">www.CryptoPanel.de</a>
            </div>
        </div>
    </footer>
</div>