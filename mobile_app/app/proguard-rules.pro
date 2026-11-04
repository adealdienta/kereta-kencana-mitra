# Add project specific ProGuard rules here.
# By default, the flags in this file are appended to flags specified
# in C:\Program Files\Android\Android Studio\plugins\android\lib\proguard\proguard-android.txt
# You can edit the include path and order by changing the proguardFiles
# directive in build.gradle.

# Retrofit & Gson rules
-keepattributes Signature
-keepattributes *Annotation*
-keep class com.keretakencana.mitra.data.model.** { *; }
-dontwarn okhttp3.**
-dontwarn retrofit2.**
