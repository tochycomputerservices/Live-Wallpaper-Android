package com.virmana.wallpaper_app;

import android.app.Application;
import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import androidx.multidex.MultiDex;

import com.applovin.sdk.AppLovinSdk;
import com.facebook.FacebookSdk;
import com.facebook.ads.AdSettings;
import com.facebook.ads.AudienceNetworkAds;
import com.facebook.appevents.AppEventsLogger;
import com.google.ads.consent.ConsentInformation;
import com.google.android.gms.ads.MobileAds;
import com.google.android.gms.ads.RequestConfiguration;
import com.onesignal.OneSignal;
import com.orhanobut.hawk.Hawk;
import com.unity3d.ads.UnityAds;

import java.util.Arrays;

import timber.log.Timber;

/**
 * Created by hsn on 27/11/2017.
 */


public class App extends Application {
    private static App instance;
    @Override
    protected void attachBaseContext(Context context) {
        super.attachBaseContext(context);
        MultiDex.install(this);
    }
    @Override
    public void onCreate()
    {
        MultiDex.install(this);
        super.onCreate();
        instance = this;
        Hawk.init(this).build();
        AudienceNetworkAds.initialize(instance);
        AppEventsLogger.activateApp(this);
        if (BuildConfig.DEBUG)
        {
            Timber.plant(new Timber.DebugTree());
        }
       Timber.i("Creating our Application");
        MobileAds.initialize(this, initializationStatus -> {});
        OneSignal.setLogLevel(OneSignal.LOG_LEVEL.VERBOSE, OneSignal.LOG_LEVEL.NONE);
        AppLovinSdk.initializeSdk(instance);
        UnityAds.initialize (this, getResources().getString(R.string.unity_ads_app_id));
        // OneSignal Initialization
        OneSignal.startInit(this)
                .inFocusDisplaying(OneSignal.OSInFocusDisplayOption.Notification)
                .unsubscribeWhenNotificationsAreDisabled(true)
                .init();
        AdSettings.addTestDevice("91d9f7ec-1923-4ec3-b6f4-8068f037d685");
        ConsentInformation.getInstance(instance).addTestDevice("2AD979608D61237C4B5AF0B7FCCE5F63");
    }

    public static App getInstance ()
    {
        return instance;
    }

    public static boolean hasNetwork ()
    {
        return instance.checkIfHasNetwork();
    }

    public boolean checkIfHasNetwork()
    {
        ConnectivityManager cm = (ConnectivityManager) getSystemService( Context.CONNECTIVITY_SERVICE);
        NetworkInfo networkInfo = cm.getActiveNetworkInfo();
        return networkInfo != null && networkInfo.isConnected();
    }
    @Override
    public void onTerminate() {
        super.onTerminate();
    }
}
