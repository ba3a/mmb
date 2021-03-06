package ru.mmb.datacollector.activity.input.data.history.list;

import ru.mmb.datacollector.R;
import android.content.Context;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filter;
import android.widget.TextView;

public class HistoryAdapter extends ArrayAdapter<HistoryListRecord>
{
	private HistoryFilter filter = null;

	public HistoryAdapter(Context context, int textViewResourceId)
	{
		super(context, textViewResourceId);
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent)
	{
		View view = convertView;
		if (view == null)
		{
			view = new HistoryRow(getContext());
		}
		HistoryListRecord item = getItem(position);
		if (item != null)
		{
			TextView tvTeamNum = (TextView) view.findViewById(R.id.inputHistoryRow_teamNumText);
			TextView tvTeamName = (TextView) view.findViewById(R.id.inputHistoryRow_teamNameText);
			TextView tvScanPointInfo =
			    (TextView) view.findViewById(R.id.inputHistoryRow_scanPointInfoText);
			TextView tvMembers = (TextView) view.findViewById(R.id.inputHistoryRow_membersText);
			TextView tvDismissed = (TextView) view.findViewById(R.id.inputHistoryRow_dismissedText);
			if (tvTeamNum != null) tvTeamNum.setText(Integer.toString(item.getTeamNumber()));
			if (tvTeamName != null) tvTeamName.setText(item.getTeam().getTeamName());
			if (tvScanPointInfo != null) tvScanPointInfo.setText(item.getScanPointInfoText());
			if (tvMembers != null) tvMembers.setText(item.getMembersText());
			if (tvDismissed != null) tvDismissed.setText(item.getDismissedText());
		}
		return view;
	}

	@Override
	public Filter getFilter()
	{
		if (filter == null)
		{
			filter = new HistoryFilter(this);
		}
		return filter;
	}
}
