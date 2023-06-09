package com.virmana.wallpaper_app.ui.activities;

import android.Manifest;
import android.app.Dialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.ServiceConnection;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.graphics.drawable.Drawable;
import android.media.MediaScannerConnection;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Environment;
import android.os.IBinder;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.core.content.FileProvider;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import androidx.appcompat.widget.AppCompatRatingBar;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.appcompat.widget.Toolbar;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Base64;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RatingBar;
import android.widget.RelativeLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;


import com.congle7997.google_iap.BillingSubs;
import com.congle7997.google_iap.CallBackBilling;
import com.facebook.ads.AbstractAdListener;
import com.facebook.ads.Ad;
import com.facebook.ads.AdError;
import com.facebook.ads.InterstitialAdListener;
import com.google.android.exoplayer2.ExoPlaybackException;
import com.google.android.exoplayer2.ExoPlayer;
import com.google.android.exoplayer2.ExoPlayerFactory;
import com.google.android.exoplayer2.PlaybackParameters;
import com.google.android.exoplayer2.Player;
import com.google.android.exoplayer2.SimpleExoPlayer;
import com.google.android.exoplayer2.Timeline;
import com.google.android.exoplayer2.extractor.DefaultExtractorsFactory;
import com.google.android.exoplayer2.extractor.ExtractorsFactory;
import com.google.android.exoplayer2.source.ExtractorMediaSource;
import com.google.android.exoplayer2.source.LoopingMediaSource;
import com.google.android.exoplayer2.source.MediaSource;
import com.google.android.exoplayer2.source.TrackGroupArray;
import com.google.android.exoplayer2.trackselection.AdaptiveTrackSelection;
import com.google.android.exoplayer2.trackselection.DefaultTrackSelector;
import com.google.android.exoplayer2.trackselection.TrackSelection;
import com.google.android.exoplayer2.trackselection.TrackSelectionArray;
import com.google.android.exoplayer2.trackselection.TrackSelector;
import com.google.android.exoplayer2.ui.SimpleExoPlayerView;
import com.google.android.exoplayer2.upstream.BandwidthMeter;
import com.google.android.exoplayer2.upstream.DataSource;
import com.google.android.exoplayer2.upstream.DefaultBandwidthMeter;
import com.google.android.exoplayer2.upstream.DefaultDataSourceFactory;
import com.google.android.exoplayer2.util.Util;
import com.google.android.gms.ads.AdListener;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdSize;
import com.google.android.gms.ads.AdView;
import com.google.android.gms.ads.FullScreenContentCallback;
import com.google.android.gms.ads.LoadAdError;
import com.google.android.gms.ads.MobileAds;

import com.google.android.gms.ads.interstitial.InterstitialAd;
import com.google.android.gms.ads.interstitial.InterstitialAdLoadCallback;
import com.google.android.gms.ads.rewarded.RewardedAd;
import com.google.android.gms.ads.rewarded.RewardedAdLoadCallback;
import com.jackandphantom.blurimage.BlurImage;
import com.kinda.progressx.ProgressWheel;
import com.orhanobut.hawk.Hawk;
import com.sothree.slidinguppanel.SlidingUpPanelLayout;
import com.squareup.picasso.Picasso;
import com.squareup.picasso.Target;
import com.virmana.wallpaper_app.R;
import com.virmana.wallpaper_app.adapter.ColorAdapter;
import com.virmana.wallpaper_app.adapter.CommentAdapter;
import com.virmana.wallpaper_app.api.apiClient;
import com.virmana.wallpaper_app.api.apiRest;
import com.virmana.wallpaper_app.config.Config;
import com.virmana.wallpaper_app.entity.ApiResponse;
import com.virmana.wallpaper_app.entity.Category;
import com.virmana.wallpaper_app.entity.Comment;
import com.virmana.wallpaper_app.entity.Wallpaper;
import com.virmana.wallpaper_app.manager.PrefManager;
import com.virmana.wallpaper_app.services.VideoLiveWallpaper;
import com.virmana.wallpaper_app.ui.view.LockableScrollView;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.UnsupportedEncodingException;
import java.net.URL;
import java.net.URLConnection;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.Map;
import java.util.NavigableMap;
import java.util.Timer;
import java.util.TimerTask;
import java.util.TreeMap;

import de.hdodenhof.circleimageview.CircleImageView;
import es.dmoral.toasty.Toasty;
import retrofit2.Call;
import retrofit2.Response;
import retrofit2.Retrofit;


public class VideoActivity extends AppCompatActivity {
    private static final int REQUEST_SET_OLD = 50001;
    private static final String DOWNLOAD_AND_SHARE = "40001";
    private static final String DOWNLOAD_ONLY = "40000";
    public static String  videoName;
    public static String videoPath;

    private InterstitialAd admobInterstitialAd;
    private com.facebook.ads.InterstitialAd facebookInterstitialAd;
    private Integer code_selected;
    // UI
    private SlidingUpPanelLayout sliding_layout_video_activit;
    private RelativeLayout relative_activity_video_layout_panel_bottom;
    private RelativeLayout relative_layout_video_activity_container;
    private ImageView image_view_video_activity_btn_share;
    private ImageView image_view_video_activity_image;
    private CardView card_view_video_activity_indicator;

    private TextView text_view_video_activity_title;
    private LinearLayout linear_layout_video_activity_apply;
    private LinearLayout linear_layout_video_activity_download;
    private LinearLayout linear_layout_wallpapar_activity_download_progress;
    private LinearLayout linear_layout_wallpapar_activity_done_download;
    private LinearLayout linear_layout_wallpapar_activity_done_apply;


    private LinearLayout linear_layout_video_activity_comment;
    private LinearLayout linear_layout_video_activity_favorite;
    private CircleImageView circle_image_view_video_activity_user;
    private TextView text_view_video_activity_name_user;
    private ImageView image_view_video_activity_trusted;
    private Button button_video_activity_follow_user;
    private TextView text_view_video_activity_description;
    private RecyclerView recycler_view_video_activity_colors;


    private AppCompatRatingBar rating_bar_video_main_video_activity;
    private AppCompatRatingBar rating_bar_video_value_video_activity;
    private RatingBar rating_bar_video_1_video_activity;
    private RatingBar rating_bar_video_2_video_activity;
    private RatingBar rating_bar_video_3_video_activity;
    private RatingBar rating_bar_video_4_video_activity;
    private RatingBar rating_bar_video_5_video_activity;
    private TextView text_view_rate_1_video_activity;
    private TextView text_view_rate_2_video_activity;
    private TextView text_view_rate_3_video_activity;
    private TextView text_view_rate_4_video_activity;
    private TextView text_view_rate_5_video_activity;
    private ProgressBar progress_bar_rate_1_video_activity;
    private ProgressBar progress_bar_rate_2_video_activity;
    private ProgressBar progress_bar_rate_3_video_activity;
    private ProgressBar progress_bar_rate_4_video_activity;
    private ProgressBar progress_bar_rate_5_video_activity;
    private TextView text_view_rate_main_video_activity;
    private LockableScrollView lockable_scroll_view_video_activity;

    private TextView text_view_video_activity_sets_count;
    private TextView text_view_video_activity_shares_count;
    private TextView text_view_video_activity_views_count;
    private TextView text_view_video_activity_downloads_count;
    private TextView text_view_video_activity_resolution;
    private TextView text_view_video_activity_type;
    private TextView text_view_video_activity_created;
    private TextView text_view_video_activity_comment_count;
    private TextView text_view_video_activity_size;
    private TextView text_view_video_activity_download_progress;
    private ProgressWheel progress_wheel_video_activity_download_progress;

    private RelativeLayout relative_layout_video_activity_comments;
    private ImageView image_view_video_activity_comment_box_close;
    private TextView text_view_video_activity_comment_count_box_count;
    private RelativeLayout relative_layout_video_activity_comment_section;
    private EditText edit_text_video_activity_comment_add;
    private ProgressBar progress_bar_video_activity_comment_add;
    private ProgressBar progress_bar_video_activity_comment_list;
    private ImageView image_button_video_activity_comment_add;
    private RecyclerView recycle_video_activity_view_comment;
    private ImageView imageView_video_activity_empty_comment;
    private RelativeLayout relative_video_activity_layout_comments;
    private RelativeLayout relative_layout_video_activity_user;
    private ProgressWheel progress_bar_video_activity_colors;
    private ImageView image_view_video_activity_fav;

    private ProgressWheel progress_wheel_video_activity_apply_progress;
    private TextView text_view_video_activity_apply_progress;
    private LinearLayout linear_layout_wallpapar_activity_apply_progress;
    private RelativeLayout relative_layout_video_activity_colors;


    private SimpleExoPlayer player;
    private SimpleExoPlayerView simpleExoPlayerView;
    // Colors
    private int statusColor;
    private int bgColor;
    // Data

    private int id;
    private String description;
    private String color;
    private String original;
    private String thumbnail;
    private String title;
    private String from;
    private String size;
    private String resolution;
    private String created;
    private int sets;
    private int shares;
    private int downloads;
    private String type;
    private int userid;
    private String username;
    private String userimage;
    private Boolean trusted;
    private int comments;
    private boolean comment;
    private String image;
    private int views;
    private String extension;
    private String tags;
    private boolean premium;
    private boolean review;
    private String kind;

    // adapters
    private CommentAdapter commentAdapter;
    private ColorAdapter colorAdapter;
    // Managers
    private LinearLayoutManager linearLayoutManagerComment;
    private GridLayoutManager gridLayoutManagerColor;
    // Lists
    private List<Comment> commentList = new ArrayList<>();
    private List<com.virmana.wallpaper_app.entity.Color> colorList = new ArrayList<>();
    // variabl;es
    private boolean colorsLoaded =  false;
    private boolean ratingsLoaded = false;

    //



    private  Boolean DialogOpened = false;


    private RewardedAd mRewardedVideoAd;
    private Boolean autoDisplay =  false;
    private Dialog dialog ;
    private PrefManager prefManager;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_video);
        getSection();
        initData();
        initColors();
        initUI();
        initWallpaper();
        initEvent();

        getUser();
        showAdsBanner();
        addView();
        this.prefManager= new PrefManager(getApplicationContext());

        if(!checkSUBSCRIBED()) {
            if (prefManager.getString("ADMIN_INTERSTITIAL_TYPE").equals("ADMOB")) {
                requestAdmobInterstitial();
            } else if (prefManager.getString("ADMIN_INTERSTITIAL_TYPE").equals("FACEBOOK")){
                requestFacebookInterstitial();
            } else if (prefManager.getString("ADMIN_INTERSTITIAL_TYPE").equals("BOTH")){
                requestAdmobInterstitial();
                requestFacebookInterstitial();
            }
        }
        initBuy();

        loadRewardedVideoAd();
        checkFavorite();
    }
    BillingSubs billingSubs;
    public void initBuy(){
        List<String> listSkuStoreSubs = new ArrayList<>();
        listSkuStoreSubs.add(Config.SUBSCRIPTION_ID);
        billingSubs = new BillingSubs(this, listSkuStoreSubs, new CallBackBilling() {
            @Override
            public void onPurchase() {
                PrefManager prefManager= new PrefManager(getApplicationContext());
                prefManager.setString("SUBSCRIBED","TRUE");
                Toasty.success(VideoActivity.this, "you have successfully subscribed ", Toast.LENGTH_SHORT).show();
            }

            @Override
            public void onNotPurchase() {
                Toasty.warning(VideoActivity.this, "Operation has been cancelled  ", Toast.LENGTH_SHORT).show();
            }

            @Override
            public void onNotLogin() {
            }
        });
    }
    public void subscribe(){
        billingSubs.purchase(Config.SUBSCRIPTION_ID);
    }
    private void initEvent() {
        this.image_view_video_activity_btn_share.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Operation(5001);
            }
        });

        this.linear_layout_video_activity_apply.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Operation(5003);

            }
        });
        this.linear_layout_video_activity_favorite.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                addFavorite();
            }
        });
        this.edit_text_video_activity_comment_add.addTextChangedListener(new VideoActivity.CommentTextWatcher(this.edit_text_video_activity_comment_add));
        this.image_button_video_activity_comment_add.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                addComment();
            }
        });
        this.relative_layout_video_activity_user.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent = new Intent(VideoActivity.this,UserActivity.class);
                intent.putExtra("id",userid);
                intent.putExtra("name",username);
                intent.putExtra("trusted",trusted);
                intent.putExtra("image",userimage);
                startActivity(intent);
            }
        });
        this.button_video_activity_follow_user.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                follow();
            }
        });
        this.linear_layout_video_activity_comment.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                showCommentBox();
            }
        });
        this.image_view_video_activity_comment_box_close.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                showCommentBox();
            }
        });
        this.linear_layout_video_activity_download.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Operation(5002);

            }
        });
        sliding_layout_video_activit.addPanelSlideListener(new SlidingUpPanelLayout.PanelSlideListener() {
            @Override
            public void onPanelSlide(View panel, float slideOffset) {
                image_view_video_activity_btn_share.setAlpha((float) 1 - slideOffset);
            }

            @Override
            public void onPanelStateChanged(View panel, SlidingUpPanelLayout.PanelState previousState, SlidingUpPanelLayout.PanelState newState) {
                if (newState == SlidingUpPanelLayout.PanelState.COLLAPSED) {
                    lockable_scroll_view_video_activity.setScrollingEnabled(false);
                    lockable_scroll_view_video_activity.fullScroll(ScrollView.FOCUS_UP);
                }
                if (newState == SlidingUpPanelLayout.PanelState.EXPANDED) {
                    lockable_scroll_view_video_activity.setScrollingEnabled(true);
                    if(!colorsLoaded)
                        getColors();
                    if (!ratingsLoaded)
                        getRating(id);
                }
            }
        });

        this.rating_bar_video_main_video_activity.setOnRatingBarChangeListener(new RatingBar.OnRatingBarChangeListener() {
            @Override
            public void onRatingChanged(RatingBar ratingBar, float rating, boolean fromUser) {
                if (fromUser) {
                    addRate(rating, id);
                }
            }
        });
    }
    private void getUser() {
        PrefManager prf= new PrefManager(VideoActivity.this.getApplicationContext());
        Integer follower= -1;
        if (prf.getString("LOGGED").toString().equals("TRUE")) {
            button_video_activity_follow_user.setEnabled(false);
            follower = Integer.parseInt(prf.getString("ID_USER"));
        }
        if (follower!=userid){
            button_video_activity_follow_user.setVisibility(View.VISIBLE);
        }
        Retrofit retrofit = apiClient.getClient();
        apiRest service = retrofit.create(apiRest.class);
        Call<ApiResponse> call = service.getUser(userid,follower);
        call.enqueue(new retrofit2.Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if (response.isSuccessful()){

                    for (int i=0;i<response.body().getValues().size();i++){
                        if (response.body().getValues().get(i).getName().equals("follow")){
                            if (response.body().getValues().get(i).getValue().equals("true"))
                                button_video_activity_follow_user.setText("UnFollow");
                            else
                                button_video_activity_follow_user.setText("Follow");
                        }
                    }

                }else{

                }
                button_video_activity_follow_user.setEnabled(true);
            }
            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                button_video_activity_follow_user.setEnabled(true);
            }
        });
    }
    public void follow(){

        PrefManager prf= new PrefManager(VideoActivity.this.getApplicationContext());
        if (prf.getString("LOGGED").toString().equals("TRUE")) {
            button_video_activity_follow_user.setText(getResources().getString(R.string.loading));
            button_video_activity_follow_user.setEnabled(false);
            String follower = prf.getString("ID_USER");
            String key = prf.getString("TOKEN_USER");
            Retrofit retrofit = apiClient.getClient();
            apiRest service = retrofit.create(apiRest.class);
            Call<ApiResponse> call = service.follow(userid, Integer.parseInt(follower), key);
            call.enqueue(new retrofit2.Callback<ApiResponse>() {
                @Override
                public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                    if (response.isSuccessful()) {
                        if (response.body().getCode().equals(200)){
                            button_video_activity_follow_user.setText("UnFollow");
                        }else if (response.body().getCode().equals(202)) {
                            button_video_activity_follow_user.setText("Follow");

                        }
                    }
                    button_video_activity_follow_user.setEnabled(true);

                }
                @Override
                public void onFailure(Call<ApiResponse> call, Throwable t) {
                    button_video_activity_follow_user.setEnabled(true);
                }
            });
        }else{
            Intent intent = new Intent(VideoActivity.this,LoginActivity.class);
            startActivity(intent);
        }
    }
    class DownloadFileFromURL extends AsyncTask<Object, String, String> {

        private String old = "-100";
        private String share_app;
        private String file_url;

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            linear_layout_wallpapar_activity_done_download.setVisibility(View.GONE);
            linear_layout_video_activity_download.setVisibility(View.GONE);
            linear_layout_wallpapar_activity_download_progress.setVisibility(View.VISIBLE);
        }
        public boolean dir_exists(String dir_path) {
            boolean ret = false;
            File dir = new File(dir_path);
            if (dir.exists() && dir.isDirectory())
                ret = true;
            return ret;
        }
        @Override
        protected void onCancelled() {
            super.onCancelled();
        }
        @Override
        protected String doInBackground(Object... f_url) {
            int count;
            try {
                URL url = new URL((String) f_url[0]);
                String title = (String) f_url[1];
                String extension = (String) f_url[2];
                this.share_app = (String) f_url[3];

                URLConnection conection = url.openConnection();
                conection.setRequestProperty("Accept-Encoding", "identity");
                conection.connect();

                int lenghtOfFile = conection.getContentLength();
                Log.v("lenghtOfFile", lenghtOfFile + "");
                InputStream input = new BufferedInputStream(url.openStream(), 8192);
                String dir_path = Environment.getExternalStorageDirectory().toString() + getResources().getString(R.string.DownloadFolder);

                if (!dir_exists(dir_path)) {
                    File directory = new File(dir_path);
                    directory.mkdirs();
                    directory.mkdir();
                }
                OutputStream output = new FileOutputStream(dir_path + title.toString().replace("/", "_") + "_" + id + "." + extension);
                byte data[] = new byte[1024];
                long total = 0;
                while ((count = input.read(data)) != -1) {
                    total += count;
                    publishProgress("" + (int) ((total * 100) / lenghtOfFile));
                    output.write(data, 0, count);
                }
                output.flush();
                output.close();
                input.close();

                this.file_url = dir_path + title.toString().replace("/", "_") + "_" + id + "." + extension;

                MediaScannerConnection.scanFile(VideoActivity.this.getApplicationContext(), new String[]{dir_path + title.toString().replace("/", "_") + "_" + id + "." + extension},
                        null,
                        new MediaScannerConnection.OnScanCompletedListener() {
                            @Override
                            public void onScanCompleted(String path, Uri uri) {
                            }
                        });
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
                    final Intent scanIntent = new Intent(Intent.ACTION_MEDIA_SCANNER_SCAN_FILE);
                    final Uri contentUri = Uri.fromFile(new File(dir_path + title.toString().replace("/", "_") + "_" + id + "." + extension));
                    scanIntent.setData(contentUri);
                    VideoActivity.this.sendBroadcast(scanIntent);
                } else {
                    final Intent intent = new Intent(Intent.ACTION_MEDIA_MOUNTED, Uri.parse("file://" + Environment.getExternalStorageDirectory()));
                    VideoActivity.this.sendBroadcast(intent);
                }
            } catch (Exception e) {
                Log.v("exdownload",e.getMessage());
            }
            return null;
        }
        protected void onProgressUpdate(String... progress) {
            try {
                if (!progress[0].equals(old)) {
                    old = progress[0];
                    float is =  (float) Float.parseFloat(progress[0]);
                    Log.v("download", progress[0] + "%");
                    progress_wheel_video_activity_download_progress.setProgress((float)(is/100));
                    text_view_video_activity_download_progress.setText(progress[0] + "%");
                }
            } catch (Exception e) {
            }
        }
        @Override
        protected void onPostExecute(String file_url) {
            linear_layout_wallpapar_activity_done_download.setVisibility(View.VISIBLE);
            linear_layout_video_activity_download.setVisibility(View.GONE);
            linear_layout_wallpapar_activity_download_progress.setVisibility(View.GONE);
            Timer myTimer = new Timer();
            myTimer.schedule(new TimerTask() {
                @Override
                public void run() {
                    // If you want to modify a view in your Activity
                    VideoActivity.this.runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            linear_layout_wallpapar_activity_done_download.setVisibility(View.GONE);
                            linear_layout_video_activity_download.setVisibility(View.VISIBLE);
                            linear_layout_wallpapar_activity_download_progress.setVisibility(View.GONE);
                        }
                    });
                }
            }, 2000);
            switch (share_app){
                case DOWNLOAD_AND_SHARE:
                    share(this.file_url);
                    break;
                default:
                    addDownload();
                    break;
            }
        }
    }
    public void share(String path){
        File externalFile=new File(path);
        Uri imageUri = FileProvider.getUriForFile(VideoActivity.this, VideoActivity.this.getApplicationContext().getPackageName() + ".provider", externalFile);
        Intent shareIntent = new Intent();
        shareIntent.setAction(Intent.ACTION_SEND);


        final String final_text = getResources().getString(R.string.download_more_from_link);

        shareIntent.putExtra(Intent.EXTRA_TEXT,final_text );
        shareIntent.putExtra(Intent.EXTRA_STREAM, imageUri);

        shareIntent.setType(type);
        shareIntent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION);
        try {
            startActivity(Intent.createChooser(shareIntent,getResources().getString(R.string.share_via)+ " " + getResources().getString(R.string.app_name) ));
        } catch (android.content.ActivityNotFoundException ex) {
            //Toasty.error(VideoActivity.this.getApplicationContext(),getResources().getString(R.string.app_not_installed) , Toast.LENGTH_SHORT, true).show();
        }
        addShare();
    }

    private void initializePlayer(){
        // Create a default TrackSelector
        BandwidthMeter bandwidthMeter = new DefaultBandwidthMeter();
        TrackSelection.Factory videoTrackSelectionFactory =
                new AdaptiveTrackSelection.Factory(bandwidthMeter);
        TrackSelector trackSelector =
                new DefaultTrackSelector(videoTrackSelectionFactory);

        //Initialize the player
        player = ExoPlayerFactory.newSimpleInstance(this, trackSelector);

        //Initialize simpleExoPlayerView
        simpleExoPlayerView = findViewById(R.id.video_view);
        simpleExoPlayerView.setPlayer(player);
        player.setVolume(0);
        // Produces DataSource instances through which media data is loaded.
        DataSource.Factory dataSourceFactory =
                new DefaultDataSourceFactory(this, Util.getUserAgent(this, "CloudinaryExoplayer"));

        // Produces Extractor instances for parsing the media data.
        ExtractorsFactory extractorsFactory = new DefaultExtractorsFactory();

        // This is the MediaSource representing the media to be played.
        Uri videoUri = Uri.parse(original);
        MediaSource videoSource = new ExtractorMediaSource(videoUri,
                dataSourceFactory, extractorsFactory, null, null);

        // Prepare the player with the source.
        LoopingMediaSource loopingSource = new LoopingMediaSource(videoSource);

        player.prepare(loopingSource);

        player.setPlayWhenReady(true);

        player.addListener(new Player.EventListener() {


            @Override
            public void onTracksChanged(TrackGroupArray trackGroups, TrackSelectionArray trackSelections) {

            }

            @Override
            public void onLoadingChanged(boolean isLoading) {

            }

            @Override
            public void onPlayerStateChanged(boolean playWhenReady, int playbackState) {
                if (playbackState == ExoPlayer.STATE_READY){
                    simpleExoPlayerView.setVisibility(View.VISIBLE);
                }
            }

            @Override
            public void onRepeatModeChanged(int repeatMode) {

            }

            @Override
            public void onPlayerError(ExoPlaybackException error) {

            }



            @Override
            public void onPlaybackParametersChanged(PlaybackParameters playbackParameters) {

            }
        });
    }
    @Override
    public void onPause() {

        super.onPause();
        if (player!=null) {
            player.release();
            player = null;
        }
    }

    @Override
    protected void onResume() {

        super.onResume();
        if (player==null) {
            initializePlayer();
        }
    }

    private void initData() {
        Bundle bundle = getIntent().getExtras();
        this.from = bundle.getString("from");

        this.id = bundle.getInt("id");
        this.color = "#" + bundle.getString("color");
        this.title = bundle.getString("title");
        this.description = bundle.getString("description");

        this.extension = bundle.getString("extension");
        this.size = bundle.getString("size");
        this.resolution = bundle.getString("resolution");
        this.created = bundle.getString("created");
        this.sets = bundle.getInt("sets");
        this.views = bundle.getInt("views");
        this.shares = bundle.getInt("shares");
        this.downloads = bundle.getInt("downloads");
        this.type = bundle.getString("type");

        this.userid = bundle.getInt("userid");
        this.username = bundle.getString("username");
        this.userimage = bundle.getString("userimage");
        this.trusted = bundle.getBoolean("trusted");

        this.comments = bundle.getInt("comments");
        this.comment = bundle.getBoolean("comment");

        this.original = bundle.getString("original");
        this.thumbnail = bundle.getString("thumbnail");
        this.image = bundle.getString("image");
        this.tags = bundle.getString("tags");
        this.premium = bundle.getBoolean("premium");
        this.review = bundle.getBoolean("review");
        this.kind = bundle.getString("kind");
    }


    private void initUI() {
        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        toolbar.setTitle("");
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);



        progress_bar_video_activity_colors = findViewById(R.id.progress_bar_video_activity_colors);
        progress_wheel_video_activity_download_progress = findViewById(R.id.progress_wheel_video_activity_download_progress);
        text_view_video_activity_download_progress = findViewById(R.id.text_view_video_activity_download_progress);
        relative_layout_video_activity_colors = findViewById(R.id.relative_layout_video_activity_colors);

        progress_wheel_video_activity_apply_progress = findViewById(R.id.progress_wheel_video_activity_apply_progress);
        text_view_video_activity_apply_progress = findViewById(R.id.text_view_video_activity_apply_progress);

        lockable_scroll_view_video_activity = findViewById(R.id.lockable_scroll_view_video_activity);
        recycler_view_video_activity_colors = findViewById(R.id.recycler_view_video_activity_colors);
        linear_layout_wallpapar_activity_done_apply = findViewById(R.id.linear_layout_wallpapar_activity_done_apply);
        linear_layout_video_activity_apply = findViewById(R.id.linear_layout_video_activity_apply);
        linear_layout_video_activity_download = findViewById(R.id.linear_layout_video_activity_download);
        linear_layout_wallpapar_activity_done_download = findViewById(R.id.linear_layout_wallpapar_activity_done_download);
        linear_layout_wallpapar_activity_apply_progress = findViewById(R.id.linear_layout_wallpapar_activity_apply_progress);
        linear_layout_wallpapar_activity_download_progress = findViewById(R.id.linear_layout_wallpapar_activity_download_progress);

        relative_layout_video_activity_user = findViewById(R.id.relative_layout_video_activity_user);
        linear_layout_video_activity_comment = findViewById(R.id.linear_layout_video_activity_comment);

        text_view_video_activity_shares_count = findViewById(R.id.text_view_video_activity_shares_count);
        text_view_video_activity_views_count = findViewById(R.id.text_view_video_activity_views_count);
        text_view_video_activity_downloads_count = findViewById(R.id.text_view_video_activity_downloads_count);

        text_view_video_activity_sets_count = findViewById(R.id.text_view_video_activity_sets_count);
        text_view_video_activity_resolution = findViewById(R.id.text_view_video_activity_resolution);
        text_view_video_activity_type = findViewById(R.id.text_view_video_activity_type);

        text_view_video_activity_created = findViewById(R.id.text_view_video_activity_created);
        text_view_video_activity_comment_count = findViewById(R.id.text_view_video_activity_comment_count);
        text_view_video_activity_size = findViewById(R.id.text_view_video_activity_size);


        button_video_activity_follow_user = findViewById(R.id.button_video_activity_follow_user);
        text_view_video_activity_name_user = findViewById(R.id.text_view_video_activity_name_user);
        image_view_video_activity_trusted = findViewById(R.id.image_view_video_activity_trusted);
        circle_image_view_video_activity_user = findViewById(R.id.circle_image_view_video_activity_user);

        linear_layout_video_activity_favorite = findViewById(R.id.linear_layout_video_activity_favorite);
        text_view_video_activity_description = findViewById(R.id.text_view_video_activity_description);
        text_view_video_activity_title = findViewById(R.id.text_view_video_activity_title);
        sliding_layout_video_activit = findViewById(R.id.sliding_layout_video_activit);
        relative_activity_video_layout_panel_bottom = findViewById(R.id.relative_activity_video_layout_panel_bottom);
        relative_layout_video_activity_container = findViewById(R.id.relative_layout_video_activity_container);
        image_view_video_activity_btn_share = findViewById(R.id.image_view_video_activity_btn_share);

        image_view_video_activity_image = findViewById(R.id.image_view_video_activity_image);
        card_view_video_activity_indicator = findViewById(R.id.card_view_video_activity_indicator);
        image_view_video_activity_fav = findViewById(R.id.image_view_video_activity_fav);


        this.rating_bar_video_main_video_activity = (AppCompatRatingBar) findViewById(R.id.rating_bar_video_main_video_activity);
        this.rating_bar_video_value_video_activity = (AppCompatRatingBar) findViewById(R.id.rating_bar_video_value_video_activity);
        this.rating_bar_video_1_video_activity = (RatingBar) findViewById(R.id.rating_bar_video_1_video_activity);
        this.rating_bar_video_2_video_activity = (RatingBar) findViewById(R.id.rating_bar_video_2_video_activity);
        this.rating_bar_video_3_video_activity = (RatingBar) findViewById(R.id.rating_bar_video_3_video_activity);
        this.rating_bar_video_4_video_activity = (RatingBar) findViewById(R.id.rating_bar_video_4_video_activity);
        this.rating_bar_video_5_video_activity = (RatingBar) findViewById(R.id.rating_bar_video_5_video_activity);

        this.text_view_rate_1_video_activity = (TextView) findViewById(R.id.text_view_rate_1_video_activity);
        this.text_view_rate_2_video_activity = (TextView) findViewById(R.id.text_view_rate_2_video_activity);
        this.text_view_rate_3_video_activity = (TextView) findViewById(R.id.text_view_rate_3_video_activity);
        this.text_view_rate_4_video_activity = (TextView) findViewById(R.id.text_view_rate_4_video_activity);
        this.text_view_rate_5_video_activity = (TextView) findViewById(R.id.text_view_rate_5_video_activity);
        this.text_view_rate_main_video_activity = (TextView) findViewById(R.id.text_view_rate_main_video_activity);
        this.progress_bar_rate_1_video_activity = (ProgressBar) findViewById(R.id.progress_bar_rate_1_video_activity);
        this.progress_bar_rate_2_video_activity = (ProgressBar) findViewById(R.id.progress_bar_rate_2_video_activity);
        this.progress_bar_rate_3_video_activity = (ProgressBar) findViewById(R.id.progress_bar_rate_3_video_activity);
        this.progress_bar_rate_4_video_activity = (ProgressBar) findViewById(R.id.progress_bar_rate_4_video_activity);
        this.progress_bar_rate_5_video_activity = (ProgressBar) findViewById(R.id.progress_bar_rate_5_video_activity);


        this.relative_layout_video_activity_comments=(RelativeLayout) findViewById(R.id.relative_layout_video_activity_comments);
        this.image_view_video_activity_comment_box_close=(ImageView) findViewById(R.id.image_view_video_activity_comment_box_close);
        this.text_view_video_activity_comment_count_box_count=(TextView) findViewById(R.id.text_view_video_activity_comment_count_box_count);


        this.relative_layout_video_activity_comment_section=(RelativeLayout) findViewById(R.id.relative_layout_video_activity_comment_section);
        this.edit_text_video_activity_comment_add=(EditText) findViewById(R.id.edit_text_video_activity_comment_add);
        this.progress_bar_video_activity_comment_add=(ProgressBar) findViewById(R.id.progress_bar_video_activity_comment_add);
        this.progress_bar_video_activity_comment_list=(ProgressBar) findViewById(R.id.progress_bar_video_activity_comment_list);
        this.image_button_video_activity_comment_add=(ImageView) findViewById(R.id.image_button_video_activity_comment_add);
        this.recycle_video_activity_view_comment=(RecyclerView) findViewById(R.id.recycle_video_activity_view_comment);
        this.imageView_video_activity_empty_comment=(ImageView) findViewById(R.id.imageView_video_activity_empty_comment);

        this.linearLayoutManagerComment = new LinearLayoutManager(getApplicationContext(),LinearLayoutManager.VERTICAL,false);
        this.gridLayoutManagerColor = new GridLayoutManager(getApplicationContext(),3);

        this.commentAdapter = new CommentAdapter(commentList, VideoActivity.this);
        this.recycle_video_activity_view_comment.setHasFixedSize(true);
        this.recycle_video_activity_view_comment.setAdapter(commentAdapter);
        this.recycle_video_activity_view_comment.setLayoutManager(linearLayoutManagerComment);

        this.colorAdapter = new ColorAdapter(colorList, VideoActivity.this);
        this.recycler_view_video_activity_colors.setHasFixedSize(true);
        this.recycler_view_video_activity_colors.setAdapter(colorAdapter);
        this.recycler_view_video_activity_colors.setLayoutManager(gridLayoutManagerColor);

        image_button_video_activity_comment_add.setEnabled(false);

    }

    @Override
    public void onBackPressed() {
        if (from == null) {
            super.onBackPressed();
            overridePendingTransition(R.anim.slide_enter, R.anim.slide_exit);
            return;
        } else {
            Intent intent = new Intent(VideoActivity.this, MainActivity.class);
            startActivity(intent);
            overridePendingTransition(R.anim.slide_enter, R.anim.slide_exit);
            finish();
        }
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case android.R.id.home:
                if (from != null) {
                    Intent intent = new Intent(VideoActivity.this, MainActivity.class);
                    startActivity(intent);
                    overridePendingTransition(R.anim.slide_enter, R.anim.slide_exit);
                    finish();
                } else {
                    super.onBackPressed();
                    overridePendingTransition(R.anim.slide_enter, R.anim.slide_exit);
                }
                return true;
        }
        return super.onOptionsItemSelected(item);
    }

    public void getRating(Integer id) {
        PrefManager prf = new PrefManager(getApplicationContext());
        String user_id = "0";
        if (prf.getString("LOGGED").toString().equals("TRUE")) {
            user_id = prf.getString("ID_USER").toString();
        }
        Retrofit retrofit = apiClient.getClient();
        apiRest service = retrofit.create(apiRest.class);
        Call<ApiResponse> call = service.getRate(user_id, id);
        call.enqueue(new retrofit2.Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if (response.isSuccessful()) {
                    if (response.body().getCode() == 200) {
                        rating_bar_video_main_video_activity.setRating(Integer.parseInt(response.body().getMessage()));
                        ratingsLoaded =  true;
                    } else if (response.body().getCode() == 202) {
                        rating_bar_video_main_video_activity.setRating(0);
                    } else {
                        rating_bar_video_main_video_activity.setRating(0);
                    }
                    if (response.body().getCode() != 500) {
                        Integer rate_1 = 0;
                        Integer rate_2 = 0;
                        Integer rate_3 = 0;
                        Integer rate_4 = 0;
                        Integer rate_5 = 0;
                        float rate = 0;
                        for (int i = 0; i < response.body().getValues().size(); i++) {

                            if (response.body().getValues().get(i).getName().equals("1")) {
                                rate_1 = Integer.parseInt(response.body().getValues().get(i).getValue());
                            }
                            if (response.body().getValues().get(i).getName().equals("2")) {
                                rate_2 = Integer.parseInt(response.body().getValues().get(i).getValue());
                            }
                            if (response.body().getValues().get(i).getName().equals("3")) {
                                rate_3 = Integer.parseInt(response.body().getValues().get(i).getValue());
                            }
                            if (response.body().getValues().get(i).getName().equals("4")) {
                                rate_4 = Integer.parseInt(response.body().getValues().get(i).getValue());
                            }
                            if (response.body().getValues().get(i).getName().equals("5")) {
                                rate_5 = Integer.parseInt(response.body().getValues().get(i).getValue());
                            }
                            if (response.body().getValues().get(i).getName().equals("rate")) {
                                rate = Float.parseFloat(response.body().getValues().get(i).getValue());
                            }
                        }
                        rating_bar_video_value_video_activity.setRating(rate);
                        String formattedString = rate + "";


                        text_view_rate_main_video_activity.setText(formattedString);
                        text_view_rate_1_video_activity.setText(rate_1 + "");
                        text_view_rate_2_video_activity.setText(rate_2 + "");
                        text_view_rate_3_video_activity.setText(rate_3 + "");
                        text_view_rate_4_video_activity.setText(rate_4 + "");
                        text_view_rate_5_video_activity.setText(rate_5 + "");
                        Integer total = rate_1 + rate_2 + rate_3 + rate_4 + rate_5;
                        if (total == 0) {
                            total = 1;
                        }
                        progress_bar_rate_1_video_activity.setProgress((int) ((rate_1 * 100) / total));
                        progress_bar_rate_2_video_activity.setProgress((int) ((rate_2 * 100) / total));
                        progress_bar_rate_3_video_activity.setProgress((int) ((rate_3 * 100) / total));
                        progress_bar_rate_4_video_activity.setProgress((int) ((rate_4 * 100) / total));
                        progress_bar_rate_5_video_activity.setProgress((int) ((rate_5 * 100) / total));
                    }
                } else {

                }

            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {


            }
        });

    }
    private void checkFavorite() {
        List<Wallpaper> favorites_list =Hawk.get("favorite");
        Boolean exist = false;
        if (favorites_list == null) {
            favorites_list = new ArrayList<>();
        }

        for (int i = 0; i < favorites_list.size(); i++) {
            if (favorites_list.get(i).getId().equals(id)) {
                exist = true;
            }
        }
        if (exist){
            image_view_video_activity_fav.setImageDrawable(getResources().getDrawable(R.drawable.ic_favorite_done));
        }else{
            image_view_video_activity_fav.setImageDrawable(getResources().getDrawable(R.drawable.ic_favorite_empty));
        }
    }
    private void addFavorite() {


        List<Wallpaper> favorites_list =Hawk.get("favorite");
        Boolean exist = false;
        if (favorites_list == null) {
            favorites_list = new ArrayList<>();
        }
        int fav_position = -1;
        for (int i = 0; i < favorites_list.size(); i++) {
            if (favorites_list.get(i).getId().equals(id)) {
                exist = true;
                fav_position = i;
            }
        }
        if (exist == false) {
            Wallpaper wallpaper = new Wallpaper();
            wallpaper.setId(id);
            wallpaper.setTitle(title);
            wallpaper.setDescription(description);
            wallpaper.setColor(color.replace("#",""));
            wallpaper.setComment(comment);
            wallpaper.setComments(comments);
            wallpaper.setCreated(created);
            wallpaper.setShares(shares);
            wallpaper.setViews(views);
            wallpaper.setSets(sets);
            wallpaper.setDownloads(downloads);
            wallpaper.setSize(size);
            wallpaper.setResolution(resolution);
            wallpaper.setType(type);
            wallpaper.setExtension(extension);
            wallpaper.setOriginal(original);
            wallpaper.setImage(image);
            wallpaper.setThumbnail(thumbnail);
            wallpaper.setKind(kind);
            wallpaper.setTags(tags);
            wallpaper.setUserimage(userimage);
            wallpaper.setUserimage(username);
            wallpaper.setUserid(userid);
            wallpaper.setPremium(premium);
            wallpaper.setReview(review);


            favorites_list.add(wallpaper);
            Hawk.put("favorite",favorites_list);
            image_view_video_activity_fav.setImageDrawable(getResources().getDrawable(R.drawable.ic_favorite_done));

        }else{
            favorites_list.remove(fav_position);
            Hawk.put("favorite",favorites_list);
            image_view_video_activity_fav.setImageDrawable(getResources().getDrawable(R.drawable.ic_favorite_empty));
        }

    }
    public void addRate(final float value, final Integer id) {
        PrefManager prf = new PrefManager(getApplicationContext());
        if (prf.getString("LOGGED").toString().equals("TRUE")) {
            Retrofit retrofit = apiClient.getClient();
            apiRest service = retrofit.create(apiRest.class);
            Call<ApiResponse> call = service.addRate(prf.getString("ID_USER").toString(), id, value);
            call.enqueue(new retrofit2.Callback<ApiResponse>() {
                @Override
                public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {

                    if (response.isSuccessful()) {
                        if (response.body().getCode() == 200) {
                            Toasty.success(VideoActivity.this, response.body().getMessage(), Toast.LENGTH_SHORT).show();
                        } else {
                            Toasty.success(VideoActivity.this, response.body().getMessage(), Toast.LENGTH_SHORT).show();
                        }
                        getRating(id);
                    } else {

                    }

                }

                @Override
                public void onFailure(Call<ApiResponse> call, Throwable t) {


                }
            });
        } else {
            Intent intent = new Intent(VideoActivity.this, LoginActivity.class);
            startActivity(intent);
        }

    }
    private void initColors() {
        float factorBg = 0.7f;
        int bg_a = Color.alpha(Color.parseColor(color));
        int bg_r = Math.round(Color.red(Color.parseColor(color)) * factorBg);
        int bg_g = Math.round(Color.green(Color.parseColor(color)) * factorBg);
        int bg_b = Math.round(Color.blue(Color.parseColor(color)) * factorBg);
        bgColor = Color.argb(bg_a,
                Math.min(bg_r, 255),
                Math.min(bg_g, 255),
                Math.min(bg_b, 255));

        float factorStatus = 0.6f;
        int Status_a = Color.alpha(Color.parseColor(color));
        int status_r = Math.round(Color.red(Color.parseColor(color)) * factorStatus);
        int status_g = Math.round(Color.green(Color.parseColor(color)) * factorStatus);
        int status_b = Math.round(Color.blue(Color.parseColor(color)) * factorStatus);
        statusColor = Color.argb(Status_a,
                Math.min(status_r, 255),
                Math.min(status_g, 255),
                Math.min(status_b, 255));

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Window window = getWindow();
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(statusColor);
        }
    }
    public void addComment(){
        PrefManager prf= new PrefManager(VideoActivity.this.getApplicationContext());
        if (prf.getString("LOGGED").toString().equals("TRUE")){

            byte[] data = new byte[0];
            String comment_final ="";
            try {
                data = edit_text_video_activity_comment_add.getText().toString().getBytes("UTF-8");
                comment_final = Base64.encodeToString(data, Base64.DEFAULT);
            } catch (UnsupportedEncodingException e) {
                comment_final = edit_text_video_activity_comment_add.getText().toString();
                e.printStackTrace();
            }
            progress_bar_video_activity_comment_add.setVisibility(View.VISIBLE);
            image_button_video_activity_comment_add.setVisibility(View.GONE);
            Retrofit retrofit = apiClient.getClient();
            apiRest service = retrofit.create(apiRest.class);
            Call<ApiResponse> call = service.addComment(prf.getString("ID_USER").toString(),id,comment_final);
            call.enqueue(new retrofit2.Callback<ApiResponse>() {
                @Override
                public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                    if (response.isSuccessful()){
                        if (response.body().getCode()==200){
                            comments++ ;
                            text_view_video_activity_comment_count_box_count.setText(comments+" "+VideoActivity.this.getResources().getString(R.string.comments));;
                            text_view_video_activity_comment_count.setText(comments+" "+VideoActivity.this.getResources().getString(R.string.comments));
                            recycle_video_activity_view_comment.setVisibility(View.VISIBLE);
                            imageView_video_activity_empty_comment.setVisibility(View.GONE);
                            Toasty.success(VideoActivity.this, response.body().getMessage(), Toast.LENGTH_SHORT).show();
                            edit_text_video_activity_comment_add.setText("");
                            String id="";
                            String content="";
                            String user="";
                            String image="";
                            String trusted="false";

                            for (int i=0;i<response.body().getValues().size();i++){
                                if (response.body().getValues().get(i).getName().equals("id")){
                                    id=response.body().getValues().get(i).getValue();
                                }
                                if (response.body().getValues().get(i).getName().equals("content")){
                                    content=response.body().getValues().get(i).getValue();
                                }
                                if (response.body().getValues().get(i).getName().equals("user")){
                                    user=response.body().getValues().get(i).getValue();
                                }
                                if (response.body().getValues().get(i).getName().equals("trusted")){
                                    trusted=response.body().getValues().get(i).getValue();
                                }
                                if (response.body().getValues().get(i).getName().equals("image")){
                                    image=response.body().getValues().get(i).getValue();
                                }
                            }
                            Comment comment= new Comment();
                            comment.setId(Integer.parseInt(id));
                            comment.setUser(user);
                            comment.setContent(content);
                            comment.setImage(image);
                            comment.setEnabled(true);
                            comment.setTrusted(trusted);
                            comment.setCreated(getResources().getString(R.string.now_time));
                            commentList.add(comment);
                            commentAdapter.notifyDataSetChanged();

                        }else{
                            Toasty.error(VideoActivity.this, response.body().getMessage(), Toast.LENGTH_SHORT).show();
                        }
                    }
                    recycle_video_activity_view_comment.scrollToPosition(recycle_video_activity_view_comment.getAdapter().getItemCount()-1);
                    recycle_video_activity_view_comment.scrollToPosition(recycle_video_activity_view_comment.getAdapter().getItemCount()-1);
                    commentAdapter.notifyDataSetChanged();
                    progress_bar_video_activity_comment_add.setVisibility(View.GONE);
                    image_button_video_activity_comment_add.setVisibility(View.VISIBLE);
                }
                @Override
                public void onFailure(Call<ApiResponse> call, Throwable t) {
                    progress_bar_video_activity_comment_add.setVisibility(View.GONE);
                    image_button_video_activity_comment_add.setVisibility(View.VISIBLE);
                }
            });
        }else{
            Intent intent = new Intent(VideoActivity.this,LoginActivity.class);
            startActivity(intent);
        }

    }
    public void getColors(){
        recycler_view_video_activity_colors.setVisibility(View.GONE);
        progress_bar_video_activity_colors.setVisibility(View.VISIBLE);
        Retrofit retrofit = apiClient.getClient();
        apiRest service = retrofit.create(apiRest.class);
        Call<List<com.virmana.wallpaper_app.entity.Color>> call = service.getColorsByWallpaper(id);
        call.enqueue(new retrofit2.Callback<List<com.virmana.wallpaper_app.entity.Color>>() {
            @Override
            public void onResponse(Call<List<com.virmana.wallpaper_app.entity.Color>> call, Response<List<com.virmana.wallpaper_app.entity.Color>> response) {
                if(response.isSuccessful()) {
                    colorList.clear();
                    if (response.body().size() != 0) {
                        for (int i = 0; i < response.body().size(); i++) {
                            colorList.add(response.body().get(i));
                        }
                        colorAdapter.notifyDataSetChanged();
                        recycler_view_video_activity_colors.setVisibility(View.VISIBLE);
                        progress_bar_video_activity_colors.setVisibility(View.GONE);
                        colorsLoaded =  true;
                    }else{
                        relative_layout_video_activity_colors.setVisibility(View.GONE);
                    }
                }else{
                    relative_layout_video_activity_colors.setVisibility(View.GONE);
                }
            }
            @Override
            public void onFailure(Call<List<com.virmana.wallpaper_app.entity.Color>> call, Throwable t) {
                recycler_view_video_activity_colors.setVisibility(View.VISIBLE);
                progress_bar_video_activity_colors.setVisibility(View.GONE);
                relative_layout_video_activity_colors.setVisibility(View.GONE);

            }
        });
    }
    public void getComments(){
        progress_bar_video_activity_comment_list.setVisibility(View.VISIBLE);
        recycle_video_activity_view_comment.setVisibility(View.GONE);
        imageView_video_activity_empty_comment.setVisibility(View.GONE);
        Retrofit retrofit = apiClient.getClient();
        apiRest service = retrofit.create(apiRest.class);
        Call<List<Comment>> call = service.getComments(id);
        call.enqueue(new retrofit2.Callback<List<Comment>>() {
            @Override
            public void onResponse(Call<List<Comment>> call, Response<List<Comment>> response) {
                if(response.isSuccessful()) {
                    commentList.clear();
                    comments = response.body().size();
                    text_view_video_activity_comment_count_box_count.setText(comments+" "+VideoActivity.this.getResources().getString(R.string.comments));;
                    text_view_video_activity_comment_count.setText(comments+" "+VideoActivity.this.getResources().getString(R.string.comments));
                    if (response.body().size() != 0) {
                        for (int i = 0; i < response.body().size(); i++) {
                            commentList.add(response.body().get(i));
                        }
                        commentAdapter.notifyDataSetChanged();
                        progress_bar_video_activity_comment_list.setVisibility(View.GONE);
                        recycle_video_activity_view_comment.setVisibility(View.VISIBLE);
                        imageView_video_activity_empty_comment.setVisibility(View.GONE);
                    } else {
                        progress_bar_video_activity_comment_list.setVisibility(View.GONE);
                        recycle_video_activity_view_comment.setVisibility(View.GONE);
                        imageView_video_activity_empty_comment.setVisibility(View.VISIBLE);
                    }
                }else{
                }
                recycle_video_activity_view_comment.setNestedScrollingEnabled(false);
            }
            @Override
            public void onFailure(Call<List<Comment>> call, Throwable t) {
            }
        });
    }
    public void showCommentBox(){
        getComments();
        if (relative_layout_video_activity_comments.getVisibility() == View.VISIBLE)
        {
            Animation c= AnimationUtils.loadAnimation(VideoActivity.this.getApplicationContext(),
                    R.anim.slide_down);
            c.setAnimationListener(new Animation.AnimationListener() {
                @Override
                public void onAnimationStart(Animation animation) {

                }
                @Override
                public void onAnimationEnd(Animation animation) {
                    relative_layout_video_activity_comments.setVisibility(View.GONE);
                }
                @Override
                public void onAnimationRepeat(Animation animation) {

                }
            });
            relative_layout_video_activity_comments.startAnimation(c);


        }else{
            Animation c= AnimationUtils.loadAnimation(VideoActivity.this.getApplicationContext(),
                    R.anim.slide_up);
            c.setAnimationListener(new Animation.AnimationListener() {
                @Override
                public void onAnimationStart(Animation animation) {
                    relative_layout_video_activity_comments.setVisibility(View.VISIBLE);
                }

                @Override
                public void onAnimationEnd(Animation animation) {
                }

                @Override
                public void onAnimationRepeat(Animation animation) {

                }
            });
            relative_layout_video_activity_comments.startAnimation(c);

        }

    }

    private static final NavigableMap<Long, String> suffixes = new TreeMap<>();

    static {
        suffixes.put(1_000L, "k");
        suffixes.put(1_000_000L, "M");
        suffixes.put(1_000_000_000L, "G");
        suffixes.put(1_000_000_000_000L, "T");
        suffixes.put(1_000_000_000_000_000L, "P");
        suffixes.put(1_000_000_000_000_000_000L, "E");
    }

    public static String format(long value) {
        //Long.MIN_VALUE == -Long.MIN_VALUE so we need an adjustment here
        if (value == Long.MIN_VALUE) return format(Long.MIN_VALUE + 1);
        if (value < 0) return "-" + format(-value);
        if (value < 1000) return Long.toString(value); //deal with easy case

        Map.Entry<Long, String> e = suffixes.floorEntry(value);
        Long divideBy = e.getKey();
        String suffix = e.getValue();

        long truncated = value / (divideBy / 10); //the number part of the output times 10
        boolean hasDecimal = truncated < 100 && (truncated / 10d) != (truncated / 10);
        return hasDecimal ? (truncated / 10d) + suffix : (truncated / 10) + suffix;
    }

    private class CommentTextWatcher implements TextWatcher {
        private View view;
        private CommentTextWatcher(View view) {
            this.view = view;
        }
        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }
        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }
        public void afterTextChanged(Editable editable) {
            switch (view.getId()) {
                case R.id.edit_text_video_activity_comment_add:
                    ValidateComment();
                    break;
            }
        }
    }
    private void initWallpaper() {
        text_view_video_activity_title.setText(title);
        text_view_video_activity_comment_count.setText(format(comments) + " Comments");
        text_view_video_activity_shares_count.setText(format(shares) + " Shares");
        text_view_video_activity_downloads_count.setText(format(downloads) + " Downloads");
        text_view_video_activity_views_count.setText(format(views) + " Views");
        text_view_video_activity_sets_count.setText(format(sets) + " Sets");
        text_view_video_activity_type.setText(type);
        text_view_video_activity_resolution.setText(resolution);
        text_view_video_activity_size.setText(size);
        text_view_video_activity_created.setText(created);
        text_view_video_activity_name_user.setText(username);
        Picasso.with(this).load(userimage).placeholder(R.drawable.profile).error(R.drawable.profile).into(circle_image_view_video_activity_user);
        if (trusted)
            image_view_video_activity_trusted.setVisibility(View.VISIBLE);
        else
            image_view_video_activity_trusted.setVisibility(View.GONE);

        if (description != null) {
            if (!description.isEmpty()){
                text_view_video_activity_description.setText(description);
                text_view_video_activity_description.setVisibility(View.VISIBLE);
            }
        }

        final Target target = new Target() {
            @Override
            public void onBitmapLoaded(Bitmap bitmap, Picasso.LoadedFrom from) {
                BlurImage.with(getApplicationContext()).load(bitmap).intensity(20).Async(true).into(image_view_video_activity_image);
            }

            @Override
            public void onBitmapFailed(Drawable errorDrawable) {

            }

            @Override
            public void onPrepareLoad(Drawable placeHolderDrawable) {

            }
        };

        Picasso.with(getApplicationContext()).load(thumbnail).error(R.drawable.placeholder).placeholder(R.drawable.placeholder).into(target);
        image_view_video_activity_image.setTag(target);
        relative_layout_video_activity_container.setBackgroundColor(Color.parseColor(color));
        card_view_video_activity_indicator.setCardBackgroundColor(statusColor);
        relative_activity_video_layout_panel_bottom.setBackgroundColor(bgColor);
    }
    private boolean ValidateComment() {
        String email = edit_text_video_activity_comment_add.getText().toString().trim();
        if (email.isEmpty()) {
            image_button_video_activity_comment_add.setEnabled(false);
            return false;
        }else{
            image_button_video_activity_comment_add.setEnabled(true);
        }
        return true;
    }
    public void set(){
        if (ContextCompat.checkSelfPermission(VideoActivity.this, Manifest.permission.READ_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED || ContextCompat.checkSelfPermission(VideoActivity.this, Manifest.permission.WRITE_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(VideoActivity.this, new String[] { Manifest.permission.READ_EXTERNAL_STORAGE, Manifest.permission.WRITE_EXTERNAL_STORAGE }, 0);
        }else{
            new VideoActivity.SetWallpaper().execute(original);
            addSet();
        }
    }
    class SetWallpaper extends AsyncTask<String, String, String> {

        private String file_url=null;
        private String old = "-100";

        /**
         * Before starting background thread
         * Show Progress Bar Dialog
         * */
        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            linear_layout_wallpapar_activity_done_apply.setVisibility(View.GONE);
            linear_layout_video_activity_apply.setVisibility(View.GONE);
            linear_layout_wallpapar_activity_apply_progress.setVisibility(View.VISIBLE);
        }
        public boolean dir_exists(String dir_path)
        {
            boolean ret = false;
            File dir = new File(dir_path);
            if(dir.exists() && dir.isDirectory())
                ret = true;
            return ret;
        }
        /**
         * Downloading file in background thread
         * */
        @Override
        protected String doInBackground(String... f_url) {
            int count;

            try {
                URL url = new URL(f_url[0]);
                URLConnection conection = url.openConnection();
                conection.setRequestProperty("Accept-Encoding", "identity");
                conection.connect();
                // this will be useful so that you can show a tipical 0-100% progress bar
                int lenghtOfFile = conection.getContentLength();

                // download the file
                InputStream input = new BufferedInputStream(url.openStream(), 8192);

                String dir_path = Environment.getExternalStorageDirectory().toString() + getResources().getString(R.string.DownloadFolder);

                if (!dir_exists(dir_path)){
                    File directory = new File(dir_path);
                    if(directory.mkdirs()){
                        Log.v("dir","is created 1");
                    }else{
                        Log.v("dir","not created 1");

                    }
                    if(directory.mkdir()){
                        Log.v("dir","is created 2");
                    }else{
                        Log.v("dir","not created 2");

                    }
                }else{
                    Log.v("dir","is exist");
                }

                // Output stream
                OutputStream output = new FileOutputStream(dir_path+title.toString().replace("/", "_") +"."+extension);

                byte data[] = new byte[1024];

                long total = 0;

                while ((count = input.read(data)) != -1) {
                    total += count;
                    // publishing the progress....
                    // After this onProgressUpdate will be called
                    publishProgress(""+(int)((total*100)/lenghtOfFile));

                    // writing data to file
                    output.write(data, 0, count);
                }

                // flushing output
                output.flush();

                output.close();
                input.close();
                this.file_url = dir_path + title.toString().replace("/", "_")  + "." + extension;
            } catch (Exception e) {
                Log.v("exdownload",e.getMessage());

            }
            return null;
        }

        /**
         * Updating progress bar
         * */
        protected void onProgressUpdate(String... progress) {
            try {
                float is =  (float) Float.parseFloat(progress[0]);
                Log.v("download", progress[0] + "%");
                progress_wheel_video_activity_apply_progress.setProgress((float)(is/100));
                text_view_video_activity_apply_progress.setText(progress[0] + "%");
            } catch (Exception e) {
            }
        }
        /**
         * After completing background task
         * Dismiss the progress dialog
         * **/
        @Override
        protected void onPostExecute(String file_url) {
            if (this.file_url==null){
                Toasty.error(getApplicationContext(),getResources().getString(R.string.error_server),Toast.LENGTH_LONG).show();
                //  toggleProgress();
            }else{
                try{
                    videoName = title.toString().replace("/", "_")  + "." + extension;
                    videoPath = Environment.getExternalStorageDirectory().toString() + getResources().getString(R.string.DownloadFolder);

                    PrefManager prf= new PrefManager(getApplicationContext());
                    prf.setString("LOCAL_VIDEO_NAME",videoName);
                    prf.setString("LOCAL_VIDEO_PATH",videoPath);

                    VideoLiveWallpaper.setToWallPaper(VideoActivity.this);
                    progress_wheel_video_activity_apply_progress.setProgress((float)(100/100));
                    text_view_video_activity_apply_progress.setText(getResources().getString(R.string.applying));
                    done();
                }catch (Exception e){
                    Log.v("exdownload",e.getMessage());
                }
            }
        }
    }
    public void getSection(){
        Retrofit retrofit = apiClient.getClient();
        apiRest service = retrofit.create(apiRest.class);
        Call<List<Category>> call = service.categoryAll();
        call.enqueue(new retrofit2.Callback<List<Category>>() {
            @Override
            public void onResponse(Call<List<Category>> call, Response<List<Category>> response) {
                if (response.isSuccessful()){
                    apiClient.FormatData(VideoActivity.this,response);
                }
            }
            @Override
            public void onFailure(Call<List<Category>> call, Throwable t) {
            }
        });
    }
    protected void done() {

        linear_layout_wallpapar_activity_done_apply.setVisibility(View.VISIBLE);
        linear_layout_video_activity_apply.setVisibility(View.GONE);
        linear_layout_wallpapar_activity_apply_progress.setVisibility(View.GONE);
        Timer myTimer = new Timer();
        myTimer.schedule(new TimerTask() {
            @Override
            public void run() {
                // If you want to modify a view in your Activity
                VideoActivity.this.runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        linear_layout_wallpapar_activity_done_apply.setVisibility(View.GONE);
                        linear_layout_video_activity_apply.setVisibility(View.VISIBLE);
                        linear_layout_wallpapar_activity_apply_progress.setVisibility(View.GONE);
                    }
                });
            }
        }, 2000);
    }
    public boolean checkSUBSCRIBED(){
        PrefManager prefManager= new PrefManager(getApplicationContext());
        if (!prefManager.getString("SUBSCRIBED").equals("TRUE")) {
            return false;
        }
        return true;
    }

    public void showAdsBanner() {
        if (!checkSUBSCRIBED()) {
            PrefManager prefManager= new PrefManager(getApplicationContext());
            if (prefManager.getString("ADMIN_BANNER_TYPE").equals("FACEBOOK")){
                showFbBanner();
            }
            if (prefManager.getString("ADMIN_BANNER_TYPE").equals("ADMOB")){
                showAdmobBanner();
            }
            if (prefManager.getString("ADMIN_BANNER_TYPE").equals("BOTH")) {
                if (prefManager.getString("Banner_Ads_display").equals("FACEBOOK")) {
                    prefManager.setString("Banner_Ads_display", "ADMOB");
                    showAdmobBanner();
                } else {
                    prefManager.setString("Banner_Ads_display", "FACEBOOK");
                    showFbBanner();
                }
            }
        }

    }
    public void showAdmobBanner(){
        PrefManager prefManager= new PrefManager(getApplicationContext());
        LinearLayout linear_layout_ads =  (LinearLayout) findViewById(R.id.linear_layout_ads);
        final AdView mAdView = new AdView(this);
        mAdView.setAdSize(AdSize.SMART_BANNER);
        mAdView.setAdUnitId(prefManager.getString("ADMIN_BANNER_ADMOB_ID"));
        AdRequest adRequest = new AdRequest.Builder()
                .build();
        mAdView.loadAd(adRequest);
        linear_layout_ads.addView(mAdView);

        final RelativeLayout relative_layout_ads = (RelativeLayout) findViewById(R.id.relative_layout_ads);

        mAdView.setAdListener(new AdListener() {
            @Override
            public void onAdLoaded() {
                super.onAdLoaded();
                mAdView.setVisibility(View.VISIBLE);
                DisplayMetrics displayMetrics = new DisplayMetrics();
                WindowManager windowManager = (WindowManager) VideoActivity.this.getSystemService(Context.WINDOW_SERVICE);
                windowManager.getDefaultDisplay().getMetrics(displayMetrics);
                RelativeLayout.LayoutParams layout_description = new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.MATCH_PARENT,Math.round(mAdView.getAdSize().getHeight() * displayMetrics.density));
                layout_description.addRule(RelativeLayout.ALIGN_PARENT_BOTTOM);
                relative_layout_ads.setLayoutParams(layout_description);
            }
        });
    }
    public void showFbBanner(){
        PrefManager prefManager= new PrefManager(getApplicationContext());
        LinearLayout linear_layout_ads =  (LinearLayout) findViewById(R.id.linear_layout_ads);
        com.facebook.ads.AdView adView = new com.facebook.ads.AdView(this, prefManager.getString("ADMIN_BANNER_FACEBOOK_ID"), com.facebook.ads.AdSize.BANNER_HEIGHT_90);


        final RelativeLayout relative_layout_ads = (RelativeLayout) findViewById(R.id.relative_layout_ads);
        linear_layout_ads.addView(adView);

        com.facebook.ads.AdListener adListener =  new AbstractAdListener() {
            @Override
            public void onAdLoaded(Ad ad) {
                super.onAdLoaded(ad);
                adView.setVisibility(View.VISIBLE);
                DisplayMetrics displayMetrics = new DisplayMetrics();
                WindowManager windowManager = (WindowManager) VideoActivity.this.getSystemService(Context.WINDOW_SERVICE);
                windowManager.getDefaultDisplay().getMetrics(displayMetrics);
                RelativeLayout.LayoutParams layout_description = new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.MATCH_PARENT,Math.round(com.facebook.ads.AdSize.BANNER_HEIGHT_90.getHeight() * displayMetrics.density));
                layout_description.addRule(RelativeLayout.ALIGN_PARENT_BOTTOM);
                relative_layout_ads.setLayoutParams(layout_description);

            }

            @Override
            public void onError(Ad ad, AdError error) {
                super.onError(ad, error);
                Log.v("BANNER_STATE",error.getErrorMessage());
            }
        };
        adView.loadAd(
                adView.buildLoadAdConfig()
                        .withAdListener(adListener)
                        .build());
    }
    public void addSet(){
        final PrefManager prefManager = new PrefManager(this);
        if (!prefManager.getString(id+"_set").equals("true")) {
            prefManager.setString(id+"_set", "true");
            Retrofit retrofit = apiClient.getClient();
            apiRest service = retrofit.create(apiRest.class);
            Call<Integer> call = service.addSet(id);
            call.enqueue(new retrofit2.Callback<Integer>() {
                @Override
                public void onResponse(Call<Integer> call, retrofit2.Response<Integer> response) {
                    if (response.isSuccessful())
                        text_view_video_activity_sets_count.setText(format(response.body())+" Sets");
                }
                @Override
                public void onFailure(Call<Integer> call, Throwable t) {
                }
            });
        }
    }
    public void addDownload(){
        final PrefManager prefManager = new PrefManager(this);
        if (!prefManager.getString(id+"_download").equals("true")) {
            prefManager.setString(id+"_download", "true");
            Retrofit retrofit = apiClient.getClient();
            apiRest service = retrofit.create(apiRest.class);
            Call<Integer> call = service.addDownload(id);
            call.enqueue(new retrofit2.Callback<Integer>() {
                @Override
                public void onResponse(Call<Integer> call, retrofit2.Response<Integer> response) {
                    if (response.isSuccessful())
                        text_view_video_activity_downloads_count.setText(format(response.body())+" Downloads");
                }
                @Override
                public void onFailure(Call<Integer> call, Throwable t) {
                }
            });
        }
    }
    public void addView(){
        final PrefManager prefManager = new PrefManager(this);
        if (!prefManager.getString(id+"_view").equals("true")) {
            prefManager.setString(id+"_view", "true");
            Retrofit retrofit = apiClient.getClient();
            apiRest service = retrofit.create(apiRest.class);
            Call<Integer> call = service.addView(id);
            call.enqueue(new retrofit2.Callback<Integer>() {
                @Override
                public void onResponse(Call<Integer> call, retrofit2.Response<Integer> response) {
                    if (response.isSuccessful())
                        text_view_video_activity_views_count.setText(format(response.body())+" Views");
                }
                @Override
                public void onFailure(Call<Integer> call, Throwable t) {
                }
            });
        }
    }
    public void addShare(){
        final PrefManager prefManager = new PrefManager(this);
        if (!prefManager.getString(id+"_share").equals("true")) {
            prefManager.setString(id+"_share", "true");
            Retrofit retrofit = apiClient.getClient();
            apiRest service = retrofit.create(apiRest.class);
            Call<Integer> call = service.addShare(id);
            call.enqueue(new retrofit2.Callback<Integer>() {
                @Override
                public void onResponse(Call<Integer> call, retrofit2.Response<Integer> response) {
                    if (response.isSuccessful())
                        text_view_video_activity_shares_count.setText(format(response.body())+" Shares");
                }
                @Override
                public void onFailure(Call<Integer> call, Throwable t) {

                }
            });
        }
    }


    public void showDialog(){
        this.dialog = new Dialog(this,
                R.style.Theme_Dialog);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setCancelable(true);
        dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.WHITE));
        dialog.setContentView(R.layout.dialog_subscribe);
        Window window = dialog.getWindow();
        WindowManager.LayoutParams wlp = window.getAttributes();
        getWindow().setLayout(ViewGroup.LayoutParams.MATCH_PARENT,ViewGroup.LayoutParams.MATCH_PARENT);
        wlp.gravity = Gravity.BOTTOM;
        wlp.flags &= ~WindowManager.LayoutParams.FLAG_DIM_BEHIND;
        window.setAttributes(wlp);
        dialog.findViewById(R.id.relative_layout_subscription).setVisibility((getResources().getString(R.string.subscription).equals( "TRUE"))? View.VISIBLE:View.GONE);
        dialog.findViewById(R.id.relative_layout_subscription_infos).setVisibility((getResources().getString(R.string.subscription).equals( "TRUE"))? View.VISIBLE:View.GONE);
        final TextView text_view_watch_ads=(TextView) dialog.findViewById(R.id.text_view_watch_ads);
        text_view_watch_ads.setText("WATCH AD TO DOWNLOAD");

        RelativeLayout relative_layout_watch_ads=(RelativeLayout) dialog.findViewById(R.id.relative_layout_watch_ads);
        relative_layout_watch_ads.setVisibility(View.VISIBLE);
        relative_layout_watch_ads.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (mRewardedVideoAd != null) {
                    mRewardedVideoAd.show(VideoActivity.this, rewardItem -> {
                        dialog.dismiss();

                        premium = false;

                        Toasty.success(getApplicationContext(),"Now you can use this premium wallpaper for free").show();
                        Log.d("Rewarded","onRewarded ");

                    });
                }else{
                    autoDisplay =  true;
                    loadRewardedVideoAd();
                    text_view_watch_ads.setText("SHOW LOADING.");
                }
            }
        });

        TextView text_view_go_pro=(TextView) dialog.findViewById(R.id.text_view_go_pro);
        text_view_go_pro.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                subscribe();
            }
        });
        dialog.setOnKeyListener(new Dialog.OnKeyListener() {

            @Override
            public boolean onKey(DialogInterface arg0, int keyCode,
                                 KeyEvent event) {
                // TODO Auto-generated method stub
                if (keyCode == KeyEvent.KEYCODE_BACK) {

                    dialog.dismiss();
                }
                return true;
            }
        });
        dialog.show();
        DialogOpened=true;

    }




    public void loadRewardedVideoAd() {
        PrefManager   prefManager= new PrefManager(getApplicationContext());

        mRewardedVideoAd.load(getApplicationContext(), prefManager.getString("ADMIN_REWARDED_ADMOB_ID"),
                new AdRequest.Builder().build(), new RewardedAdLoadCallback() {
                    @Override
                    public void onAdLoaded(@NonNull RewardedAd rewardedAd) {
                        super.onAdLoaded(rewardedAd);

                        if (autoDisplay){
                            dialog.dismiss();
                            autoDisplay = false;
                            mRewardedVideoAd = rewardedAd;
                            mRewardedVideoAd.show(VideoActivity.this, rewardItem -> {
                                dialog.dismiss();

                                premium =  false;
                                dialog.dismiss();
                                Toasty.success(getApplicationContext(),"Now you can use this premium wallpaper for free").show();
                                Log.d("Rewarded","onRewarded ");

                            });
                        }
                    }

                    @Override
                    public void onAdFailedToLoad(@NonNull LoadAdError loadAdError) {
                        super.onAdFailedToLoad(loadAdError);
                        mRewardedVideoAd = null;

                    }
                });
    }

    private void requestAdmobInterstitial() {
        if (admobInterstitialAd==null){
            PrefManager prefManager= new PrefManager(this);
            AdRequest adRequest = new AdRequest.Builder().build();
            admobInterstitialAd.load(getApplicationContext(), prefManager.getString("ADMIN_INTERSTITIAL_ADMOB_ID"), adRequest, new InterstitialAdLoadCallback() {
                @Override
                public void onAdLoaded(@NonNull InterstitialAd interstitialAd) {
                    super.onAdLoaded(interstitialAd);
                    admobInterstitialAd = interstitialAd;


                    admobInterstitialAd.setFullScreenContentCallback(new FullScreenContentCallback(){
                        @Override
                        public void onAdDismissedFullScreenContent() {
                            selectOperation(code_selected);

                            Log.d("TAG", "The ad was dismissed.");
                        }


                        @Override
                        public void onAdShowedFullScreenContent() {
                            admobInterstitialAd = null;
                            Log.d("TAG", "The ad was shown.");
                        }
                    });

                }

                @Override
                public void onAdFailedToLoad(@NonNull LoadAdError loadAdError) {
                    super.onAdFailedToLoad(loadAdError);
                    admobInterstitialAd = null;
                    Log.d("TAG_ADS", "onAdFailedToLoad: "+loadAdError.getMessage());

                }
            });

        }


    }


    private void requestFacebookInterstitial() {
        if (facebookInterstitialAd==null) {
            PrefManager prefManager= new PrefManager(this);
            facebookInterstitialAd = new com.facebook.ads.InterstitialAd(this, prefManager.getString("ADMIN_INTERSTITIAL_FACEBOOK_ID"));
        }
        if (!facebookInterstitialAd.isAdLoaded()) {
            InterstitialAdListener interstitialAdListener = new InterstitialAdListener() {
                @Override
                public void onInterstitialDisplayed(Ad ad) {
                }

                @Override
                public void onInterstitialDismissed(Ad ad) {
                    selectOperation(code_selected);
                }

                @Override
                public void onError(Ad ad, AdError adError) {
                }

                @Override
                public void onAdLoaded(Ad ad) {
                }

                @Override
                public void onAdClicked(Ad ad) {
                }

                @Override
                public void onLoggingImpression(Ad ad) {
                }
            };
            facebookInterstitialAd.loadAd(
                    facebookInterstitialAd.buildLoadAdConfig()
                            .withAdListener(interstitialAdListener)
                            .build());
        }
    }
    public void Operation(Integer code){
        PrefManager prefManager= new PrefManager(this);


        if (!premium){
            if(checkSUBSCRIBED()) {
                selectOperation(code);
            }else{
                if( prefManager.getString("ADMIN_INTERSTITIAL_TYPE").equals("ADMOB")) {
                    requestAdmobInterstitial();
                    if(prefManager.getInt("ADMIN_INTERSTITIAL_CLICKS")<=prefManager.getInt("ADMOB_INTERSTITIAL_COUNT_CLICKS")){
                        if (admobInterstitialAd != null) {
                            prefManager.setInt("ADMOB_INTERSTITIAL_COUNT_CLICKS",0);
                            admobInterstitialAd.show(VideoActivity.this);
                            code_selected = code;
                        }else{
                            selectOperation(code);
                        }
                    }else{
                        selectOperation(code);
                        prefManager.setInt("ADMOB_INTERSTITIAL_COUNT_CLICKS",prefManager.getInt("ADMOB_INTERSTITIAL_COUNT_CLICKS")+1);

                    }
                }else if(prefManager.getString("ADMIN_INTERSTITIAL_TYPE").equals("FACEBOOK")){
                    requestFacebookInterstitial();
                    if(prefManager.getInt("ADMIN_INTERSTITIAL_CLICKS")<=prefManager.getInt("ADMOB_INTERSTITIAL_COUNT_CLICKS")) {
                        if (facebookInterstitialAd.isAdLoaded()) {
                            prefManager.setInt("ADMOB_INTERSTITIAL_COUNT_CLICKS",0);
                            facebookInterstitialAd.show();
                            code_selected = code;
                        }else{
                            selectOperation(code);
                        }
                    }else{
                        selectOperation(code);
                        prefManager.setInt("ADMOB_INTERSTITIAL_COUNT_CLICKS",prefManager.getInt("ADMOB_INTERSTITIAL_COUNT_CLICKS")+1);

                    }
                }else if(prefManager.getString("ADMIN_INTERSTITIAL_TYPE").equals("BOTH")) {
                    requestAdmobInterstitial();
                    requestFacebookInterstitial();

                    if(prefManager.getInt("ADMIN_INTERSTITIAL_CLICKS")<=prefManager.getInt("ADMOB_INTERSTITIAL_COUNT_CLICKS")) {
                        if (prefManager.getString("AD_INTERSTITIAL_SHOW_TYPE").equals("ADMOB")){
                            prefManager.setInt("ADMOB_INTERSTITIAL_COUNT_CLICKS",0);
                            prefManager.setString("AD_INTERSTITIAL_SHOW_TYPE","FACEBOOK");
                            if (admobInterstitialAd != null) {

                                admobInterstitialAd.show(VideoActivity.this);
                                code_selected = code;
                            }else{
                                selectOperation(code);
                                requestFacebookInterstitial();
                            }
                        }else{
                            prefManager.setInt("ADMOB_INTERSTITIAL_COUNT_CLICKS",0);
                            prefManager.setString("AD_INTERSTITIAL_SHOW_TYPE","ADMOB");
                            if (facebookInterstitialAd.isAdLoaded()) {

                                facebookInterstitialAd.show();
                                code_selected = code;

                            }else{
                                selectOperation(code);
                            }
                        }
                    }else{
                        selectOperation(code);
                        prefManager.setInt("ADMOB_INTERSTITIAL_COUNT_CLICKS",prefManager.getInt("ADMOB_INTERSTITIAL_COUNT_CLICKS")+1);
                    }
                }else{
                    selectOperation(code);
                }
            }
        }else{
            if (checkSUBSCRIBED()) {
                selectOperation(code);
            }else{
                showDialog();
            }
        }
    }

    private void selectOperation(Integer code) {
        switch (code){
            case 5001:{
                new VideoActivity.DownloadFileFromURL().executeOnExecutor(AsyncTask.THREAD_POOL_EXECUTOR,original, title,extension,DOWNLOAD_AND_SHARE);
                break;
            }
            case 5002:{
                new VideoActivity.DownloadFileFromURL().executeOnExecutor(AsyncTask.THREAD_POOL_EXECUTOR,original, title,extension,DOWNLOAD_ONLY);
                break;
            }
            case 5003:{
                set();
                break;
            }
        }
    }
}
