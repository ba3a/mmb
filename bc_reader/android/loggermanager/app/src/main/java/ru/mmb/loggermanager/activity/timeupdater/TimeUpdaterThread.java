package ru.mmb.loggermanager.activity.timeupdater;

import android.bluetooth.BluetoothAdapter;
import android.bluetooth.BluetoothDevice;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.widget.ProgressBar;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Set;

import ru.mmb.loggermanager.activity.MainActivity;
import ru.mmb.loggermanager.activity.settings.LoggerSettingsBluetoothClient;
import ru.mmb.loggermanager.bluetooth.DeviceInfo;

public class TimeUpdaterThread extends Thread {

    private final MainActivity owner;
    private final List<DeviceInfo> pairedLoggers;
    private final Handler timeUpdateHandler;

    private volatile boolean terminated = false;

    public TimeUpdaterThread(MainActivity owner, Handler timeUpdateHandler) {
        super("time updater thread");
        this.owner = owner;
        this.pairedLoggers = loadPairedDevices();
        this.timeUpdateHandler = timeUpdateHandler;
    }

    @Override
    public void run() {
        Log.d("TIME_UPDATER", "time updater thread started");
        while (!terminated) {
            final ProgressBar progressBar = owner.getTimeUpdaterProgress();
            if (progressBar != null) {
                progressBar.post(new Runnable() {
                    @Override
                    public void run() {
                        progressBar.setVisibility(View.VISIBLE);
                        Log.d("TIME_UPDATER", "progress bar VISIBLE");
                    }
                });
                // send time update command to all loggers
                for (DeviceInfo pairedLogger : pairedLoggers) {
                    LoggerSettingsBluetoothClient btClient = new LoggerSettingsBluetoothClient(owner, pairedLogger, timeUpdateHandler, null);
                    btClient.updateLoggerTime();
                }
                Log.d("TIME_UPDATER", "SENT new time to loggers");
                progressBar.post(new Runnable() {
                    @Override
                    public void run() {
                        progressBar.setVisibility(View.INVISIBLE);
                        Log.d("TIME_UPDATER", "progress bar INVISIBLE");
                    }
                });
            }
            try {
                Thread.sleep(10000L);
            } catch (InterruptedException e) {
                Log.d("TIME_UPDATER", "time updater thread interrupted");
            }
        }
        Log.d("TIME_UPDATER", "time updater thread finished");
    }

    public void terminate() {
        terminated = true;
    }

    private List<DeviceInfo> loadPairedDevices() {
        List<DeviceInfo> result = new ArrayList<DeviceInfo>();
        BluetoothAdapter btAdapter = BluetoothAdapter.getDefaultAdapter();
        Set<BluetoothDevice> devices = btAdapter.getBondedDevices();
        for (BluetoothDevice device : devices) {
            if (device.getName().contains("LOGGER")) {
                result.add(new DeviceInfo(device.getName(), device.getAddress()));
            }
        }
        Collections.sort(result);
        return result;
    }
}
