<!-- GDPR Consent Banner -->
<div class="gdpr-consent-banner" id="gdprConsentBanner">
    <div class="gdpr-consent-content">
        <div class="gdpr-consent-text">
            <h3><?php _e('🍪 We Value Your Privacy', 'textdomain'); ?></h3>
            <p>
                <?php _e('We use cookies and similar technologies to improve your browsing experience, analyze site traffic, and personalize content. This includes Google Analytics, Google Ads, and other Google services that process personal data from users in the European Economic Area.', 'textdomain'); ?>
            </p>
            <p>
                <?php _e('By clicking "Accept All", you consent to our use of cookies and data processing. You can customize your preferences or reject non-essential cookies.', 'textdomain'); ?>
                <br>
                <a href="<?php echo esc_url(get_privacy_policy_url()); ?>" target="_blank"><?php _e('Privacy Policy', 'textdomain'); ?></a> | 
                <a href="<?php echo esc_url(home_url('/cookie-policy/')); ?>" target="_blank"><?php _e('Cookie Policy', 'textdomain'); ?></a>
            </p>
        </div>
        <div class="gdpr-consent-buttons">
            <button type="button" class="gdpr-btn gdpr-btn-accept-all" id="gdprAcceptAll">
                <?php _e('Accept All', 'textdomain'); ?>
            </button>
            <button type="button" class="gdpr-btn gdpr-btn-reject-all" id="gdprRejectAll">
                <?php _e('Reject Non-Essential', 'textdomain'); ?>
            </button>
            <button type="button" class="gdpr-btn gdpr-btn-settings" id="gdprShowSettings">
                <?php _e('Cookie Settings', 'textdomain'); ?>
            </button>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="gdpr-settings-modal" id="gdprSettingsModal">
    <div class="gdpr-modal-content">
        <div class="gdpr-modal-header">
            <h3><?php _e('Cookie & Privacy Settings', 'textdomain'); ?></h3>
            <button type="button" class="gdpr-modal-close" id="gdprModalClose">&times;</button>
        </div>
        <div class="gdpr-modal-body">
            <p><?php _e('Manage your consent preferences for different types of cookies and data processing:', 'textdomain'); ?></p>
            
            <!-- Essential Cookies -->
            <div class="gdpr-consent-category">
                <div class="gdpr-category-header">
                    <h4><?php _e('Essential Cookies', 'textdomain'); ?></h4>
                    <span class="gdpr-always-active"><?php _e('Always Active', 'textdomain'); ?></span>
                </div>
                <div class="gdpr-category-description">
                    <p><?php _e('These cookies are necessary for the website to function and cannot be switched off. They are usually only set in response to actions made by you which amount to a request for services.', 'textdomain'); ?></p>
                </div>
            </div>

            <!-- Analytics -->
            <div class="gdpr-consent-category">
                <div class="gdpr-category-header">
                    <h4><?php _e('Analytics & Performance', 'textdomain'); ?></h4>
                    <label class="gdpr-toggle">
                        <input type="checkbox" id="gdprAnalytics" name="analytics">
                        <span class="gdpr-slider"></span>
                    </label>
                </div>
                <div class="gdpr-category-description">
                    <p><?php _e('These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously. This includes Google Analytics.', 'textdomain'); ?></p>
                    <small><strong><?php _e('Services:', 'textdomain'); ?></strong> Google Analytics, Google Tag Manager</small>
                </div>
            </div>

            <!-- Advertising -->
            <div class="gdpr-consent-category">
                <div class="gdpr-category-header">
                    <h4><?php _e('Advertising & Targeting', 'textdomain'); ?></h4>
                    <label class="gdpr-toggle">
                        <input type="checkbox" id="gdprAdvertising" name="advertising">
                        <span class="gdpr-slider"></span>
                    </label>
                </div>
                <div class="gdpr-category-description">
                    <p><?php _e('These cookies are used to make advertising messages more relevant to you and your interests. They also perform functions like preventing the same ad from continuously reappearing.', 'textdomain'); ?></p>
                    <small><strong><?php _e('Services:', 'textdomain'); ?></strong> Google Ads, Google AdSense, Remarketing</small>
                </div>
            </div>

            <!-- Personalization -->
            <div class="gdpr-consent-category">
                <div class="gdpr-category-header">
                    <h4><?php _e('Personalization', 'textdomain'); ?></h4>
                    <label class="gdpr-toggle">
                        <input type="checkbox" id="gdprPersonalization" name="personalization">
                        <span class="gdpr-slider"></span>
                    </label>
                </div>
                <div class="gdpr-category-description">
                    <p><?php _e('These cookies enable us to provide personalized content and remember your preferences to improve your user experience.', 'textdomain'); ?></p>
                    <small><strong><?php _e('Services:', 'textdomain'); ?></strong> User preferences, Language settings, Personalized content</small>
                </div>
            </div>
        </div>
        <div class="gdpr-modal-footer">
            <button type="button" class="gdpr-btn gdpr-btn-reject-all" id="gdprSaveRejectAll">
                <?php _e('Save & Reject All', 'textdomain'); ?>
            </button>
            <button type="button" class="gdpr-btn gdpr-btn-accept" id="gdprSaveSettings">
                <?php _e('Save Settings', 'textdomain'); ?>
            </button>
        </div>
    </div>
</div>

<!-- Consent Status (for testing) -->
<?php if (WP_DEBUG) : ?>
<div class="gdpr-debug-status" id="gdprDebugStatus">
    Consent Status: Loading...
</div>
<?php endif; ?>