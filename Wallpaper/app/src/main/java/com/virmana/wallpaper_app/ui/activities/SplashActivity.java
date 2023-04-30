package com.virmana.wallpaper_app.ui.activities;

import android.Manifest;
import android.app.Activity;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.ServiceConnection;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Build;
import android.os.IBinder;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.core.content.ContextCompat;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.congle7997.google_iap.BillingSubs;
import com.congle7997.google_iap.CallBackCheck;
import com.virmana.wallpaper_app.R;
import com.virmana.wallpaper_app.api.apiClient;
import com.virmana.wallpaper_app.api.apiRest;
import com.virmana.wallpaper_app.config.Config;
import com.virmana.wallpaper_app.entity.ApiResponse;
import com.virmana.wallpaper_app.entity.Category;
import com.virmana.wallpaper_app.manager.PrefManager;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;
import java.util.Timer;
import java.util.TimerTask;

import es.dmoral.toasty.Toasty;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;


public class SplashActivity extends AppCompatActivity {

    private ProgressBar intro_progress;
    private PrefManager prf;




    private static final String LOG_TAG = "iabv3";
// PUT YOUR MERCHANT KEY HERE;
    // put your Google merchant id here (as stated in public profile of your Payments Merchant Center)
    // if filled library will provide protection against Freedom alike Play Market simulators
    private static final String MERCHANT_ID=null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        check();

        setContentView(R.layout.activity_splash);
        getSection();
        prf= new PrefManager(getApplicationContext());

        Timer myTimer = new Timer();
        myTimer.schedule(new TimerTask() {
            @Override
            public void run() {
                // If you want to modify a view in your Activity
                SplashActivity.this.runOnUiThread(new Runnable() {
                    @Override
                    public void run() {


                        checkAccount();
                    }
                });
            }
        }, 1000);

        prf.setString("ADMIN_REWARDED_ADMOB_ID","");

        prf.setString("ADMIN_INTERSTITIAL_ADMOB_ID","");
        prf.setString("ADMIN_INTERSTITIAL_FACEBOOK_ID","");
        prf.setString("ADMIN_INTERSTITIAL_TYPE","FALSE");
        prf.setInt("ADMIN_INTERSTITIAL_CLICKS",3);

        prf.setString("ADMIN_BANNER_ADMOB_ID","");
        prf.setString("ADMIN_BANNER_FACEBOOK_ID","");
        prf.setString("ADMIN_BANNER_TYPE","FALSE");

        prf.setString("ADMIN_NATIVE_FACEBOOK_ID","");
        prf.setString("ADMIN_NATIVE_ADMOB_ID","");
        prf.setString("ADMIN_NATIVE_LINES","6");
        prf.setString("ADMIN_NATIVE_TYPE","FALSE");
        prf.setString("ADMIN_PUBLISHER_ID","");

    }
    public static void adapteActivity(Activity activity){
        activity.finish();
    }
    private void checkAccount() {

        Integer version = -1;
        try {

            PackageInfo pInfo = this.getPackageManager().getPackageInfo(getPackageName(), 0);
            version = pInfo.versionCode;
        } catch (PackageManager.NameNotFoundException e) {
            e.printStackTrace();

        }

        if (version!=-1){


            Retrofit retrofit = apiClient.getClient();
            apiRest service = retrofit.create(apiRest.class);
            Call<ApiResponse> call = service.check(version);
            call.enqueue(new Callback<ApiResponse>() {
                @Override
                public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {

                    apiClient.FormatData(SplashActivity.this,response);
                    if (response.isSuccessful()){

                        for (int i = 0; i < response.body().getValues().size(); i++) {
                            Log.v("DATAADS",response.body().getValues().get(i).getName() + " == "+ response.body().getValues().get(i).getValue());
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_REWARDED_ADMOB_ID") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_REWARDED_ADMOB_ID",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_PUBLISHER_ID") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_PUBLISHER_ID",response.body().getValues().get(i).getValue());
                            }

                            if ( response.body().getValues().get(i).getName().equals("ADMIN_INTERSTITIAL_ADMOB_ID") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_INTERSTITIAL_ADMOB_ID",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_INTERSTITIAL_FACEBOOK_ID") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_INTERSTITIAL_FACEBOOK_ID",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_INTERSTITIAL_TYPE") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_INTERSTITIAL_TYPE",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_INTERSTITIAL_CLICKS") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setInt("ADMIN_INTERSTITIAL_CLICKS",Integer.parseInt(response.body().getValues().get(i).getValue()));
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_BANNER_ADMOB_ID") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_BANNER_ADMOB_ID",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_BANNER_FACEBOOK_ID") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_BANNER_FACEBOOK_ID",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_BANNER_TYPE") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_BANNER_TYPE",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_NATIVE_FACEBOOK_ID") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_NATIVE_FACEBOOK_ID",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_NATIVE_ADMOB_ID") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_NATIVE_ADMOB_ID",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_NATIVE_LINES") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_NATIVE_LINES",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("ADMIN_NATIVE_TYPE") ){
                                if (response.body().getValues().get(i).getValue()!=null)
                                    prf.setString("ADMIN_NATIVE_TYPE",response.body().getValues().get(i).getValue());
                            }
                            if ( response.body().getValues().get(i).getName().equals("user") ){
                                if (response.body().getValues().get(i).getValue().equals("403")){
                                    prf.remove("ID_USER");
                                    prf.remove("SALT_USER");
                                    prf.remove("TOKEN_USER");
                                    prf.remove("NAME_USER");
                                    prf.remove("TYPE_USER");
                                    prf.remove("USERN_USER");
                                    prf.remove("IMAGE_USER");
                                    prf.remove("LOGGED");
                                    Toasty.error(getApplicationContext(),getResources().getString(R.string.account_disabled), Toast.LENGTH_SHORT, true).show();
                                }
                            }
                        }


                        if (response.body().getCode().equals(200)) {
                            if (!prf.getString("first").equals("true")){
                                Intent intent = new Intent(SplashActivity.this,IntroActivity.class);
                                startActivity(intent);
                                overridePendingTransition(R.anim.enter, R.anim.exit);
                                finish();
                                prf.setString("first","true");
                            }else{

                                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                                    if (
                                            ContextCompat.checkSelfPermission(SplashActivity.this, Manifest.permission.READ_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED ||
                                            ContextCompat.checkSelfPermission(SplashActivity.this, Manifest.permission.WRITE_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED
                                            ) {
                                        Intent intent = new Intent(SplashActivity.this,PermissionActivity.class);
                                        startActivity(intent);
                                        overridePendingTransition(R.anim.enter, R.anim.exit);
                                        finish();
                                    }else{
                                        Intent intent = new Intent(SplashActivity.this,MainActivity.class);
                                        startActivity(intent);
                                        overridePendingTransition(R.anim.enter, R.anim.exit);
                                        finish();
                                    }
                                }else{
                                    Intent intent = new Intent(SplashActivity.this,MainActivity.class);
                                    startActivity(intent);
                                    overridePendingTransition(R.anim.enter, R.anim.exit);
                                    finish();
                                }

                            }
                        }else if (response.body().getCode().equals(202)) {


                            String title_update=response.body().getValues().get(0).getValue();
                            String featurs_update=response.body().getMessage();

                            View v = (View)  getLayoutInflater().inflate(R.layout.update_message,null);
                            TextView update_text_view_title=(TextView) v.findViewById(R.id.update_text_view_title);
                            TextView update_text_view_updates=(TextView) v.findViewById(R.id.update_text_view_updates);
                            update_text_view_title.setText(title_update);
                            update_text_view_updates.setText(featurs_update);
                            AlertDialog.Builder builder;
                            builder = new AlertDialog.Builder(SplashActivity.this);
                            builder.setTitle("New Update")
                                    //.setMessage(response.body().getValue())
                                    .setView(v)
                                    .setPositiveButton(getResources().getString(R.string.update_now), new DialogInterface.OnClickListener() {
                                        public void onClick(DialogInterface dialog, int which) {
                                            final String appPackageName=getApplication().getPackageName();
                                            try {
                                                startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=" + appPackageName)));
                                            } catch (android.content.ActivityNotFoundException anfe) {
                                                startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("http://play.google.com/store/apps/details?id=" + appPackageName)));
                                            }
                                            finish();
                                        }
                                    })
                                    .setNegativeButton(getResources().getString(R.string.skip), new DialogInterface.OnClickListener() {
                                        public void onClick(DialogInterface dialog, int which) {
                                            if (!prf.getString("first").equals("true")){
                                                Intent intent = new Intent(SplashActivity.this,IntroActivity.class);
                                                startActivity(intent);
                                                overridePendingTransition(R.anim.enter, R.anim.exit);
                                                finish();
                                                prf.setString("first","true");
                                            }else{
                                                Intent intent = new Intent(SplashActivity.this,MainActivity.class);
                                                startActivity(intent);
                                                overridePendingTransition(R.anim.enter, R.anim.exit);
                                                finish();
                                            }
                                        }
                                    })
                                    .setCancelable(false)
                                    .setIcon(R.drawable.ic_update)
                                    .show();

                        } else {
                            if (!prf.getString("first").equals("true")){
                                Intent intent = new Intent(SplashActivity.this,IntroActivity.class);
                                startActivity(intent);
                                overridePendingTransition(R.anim.enter, R.anim.exit);
                                finish();
                                prf.setString("first","true");
                            }else{
                                Intent intent = new Intent(SplashActivity.this,MainActivity.class);
                                startActivity(intent);
                                overridePendingTransition(R.anim.enter, R.anim.exit);
                               finish();
                            }

                        }
                    }else {
                        if (!prf.getString("first").equals("true")){
                            Intent intent = new Intent(SplashActivity.this,IntroActivity.class);
                            startActivity(intent);
                            overridePendingTransition(R.anim.enter, R.anim.exit);
                           finish();
                            prf.setString("first","true");
                        }else{
                            Intent intent = new Intent(SplashActivity.this,MainActivity.class);
                            startActivity(intent);
                            overridePendingTransition(R.anim.enter, R.anim.exit);
                           finish();
                        }

                    }
                  /*  if (response.body().getName().equals("true")){


                        if (!prf.getString("first").equals("true")){
                            Intent intent = new Intent(IntroActivity.this,SlideActivity.class);
                            startActivity(intent);
                            overridePendingTransition(R.anim.enter, R.anim.exit);
                            finish();
                            prf.setString("first","true");
                        }else{
                            Intent intent = new Intent(IntroActivity.this,MainActivity.class);
                            startActivity(intent);
                            overridePendingTransition(R.anim.enter, R.anim.exit);
                            finish();
                        }




                    }else if(response.body().getName().equals("false")){
                    */

                    /*
                    }else {
                        if (!prf.getString("first").equals("true")){
                            Intent intent = new Intent(IntroActivity.this,SlideActivity.class);
                            startActivity(intent);
                            overridePendingTransition(R.anim.enter, R.anim.exit);
                            finish();
                            prf.setString("first","true");
                        }else{
                            Intent intent = new Intent(IntroActivity.this,MainActivity.class);
                            startActivity(intent);
                            overridePendingTransition(R.anim.enter, R.anim.exit);
                            finish();
                        }

                    }*/


                }
                @Override
                public void onFailure(Call<ApiResponse> call, Throwable t) {

                    if (!prf.getString("first").equals("true")){
                        Intent intent = new Intent(SplashActivity.this,IntroActivity.class);
                        startActivity(intent);
                        overridePendingTransition(R.anim.enter, R.anim.exit);
                        finish();
                        prf.setString("first","true");
                    }else{
                        Intent intent = new Intent(SplashActivity.this,MainActivity.class);
                        startActivity(intent);
                        overridePendingTransition(R.anim.enter, R.anim.exit);
                        finish();
                    }

                }
            });
        }else{
            if (!prf.getString("first").equals("true")){
                Intent intent = new Intent(SplashActivity.this,IntroActivity.class);
                startActivity(intent);
                overridePendingTransition(R.anim.enter, R.anim.exit);
                finish();
                prf.setString("first","true");
            }else{
                Intent intent = new Intent(SplashActivity.this,MainActivity.class);
                startActivity(intent);
                overridePendingTransition(R.anim.enter, R.anim.exit);
                finish();
            }

        }

    }
    private void getSection() {
        Retrofit retrofit = apiClient.getClient();
        apiRest service = retrofit.create(apiRest.class);
        Call<List<Category>> call = service.categoryAll();
        call.enqueue(new Callback<List<Category>>() {
            @Override
            public void onResponse(Call<List<Category>> call, Response<List<Category>> response) {


            }
            @Override
            public void onFailure(Call<List<Category>> call, Throwable t) {

            }
        });
    }
    public void check(){
        List<String> listSkuStoreSubs = new ArrayList<>();
        listSkuStoreSubs.add(Config.SUBSCRIPTION_ID);
        new BillingSubs(SplashActivity.this, listSkuStoreSubs, new CallBackCheck() {
            @Override
            public void onPurchase() {
                PrefManager prefManager= new PrefManager(getApplicationContext());
                prefManager.setString("SUBSCRIBED","TRUE");
            }

            @Override
            public void onNotPurchase() {
                PrefManager prefManager= new PrefManager(getApplicationContext());
                prefManager.setString("SUBSCRIBED","FALSE");
            }
        });
    }

}
